# Prompt: migración Flyway ms-planifications — expediciones y servicios (abril 2026)

**Uso:** copia el bloque **«Actúa como…»** hasta el final y pégalo en un asistente de IA o entrégalo a un desarrollador. El resultado esperado es un **fichero SQL de migración Flyway** versionado en `ms-planifications/src/main/resources/db/migration/` (p. ej. `V2__seed_expeditions_services_april_2026.sql`).

---

## Bloque a ejecutar

Actúa como **desarrollador senior SQL / MySQL 8** familiarizado con **Flyway** y arquitectura **database-per-service**. Tu misión es **diseñar e implementar una migración de datos** en la base **`ms-planifications`** que permita al **frontend** consumir **horarios y servicios concretos** para **todo el mes de abril de 2026** (fechas `2026-04-01` … `2026-04-30` inclusive), enlazados de forma **lógica** con rutas y paradas que viven en **`ms-router`**.

### Restricciones obligatorias

1. **Solo tocar la BD `ms-planifications`.** No crear FKs cruzadas hacia `ms-router`; las referencias son **UUID en VARCHAR(36)** sin FK inter-BD.

2. **Tablas a poblar (mínimo):**
   - **`expeditions`** — plantilla operativa por ruta (snapshot).
   - **`expedition_stops`** — paradas de la expedición con **`offset_seconds`** respecto a **`base_time`**.
   - **`planifications`** — ventana que cubra **abril 2026** (`date_from`, `date_until`) y enlace a la expedición (**obligatorio**: la tabla **`services`** tiene `FOREIGN KEY (planification_id) → planifications`).
   - **`services`** — **una fila por cada día laborable (o cada día, según decisión documentada) en abril 2026** para cada expedición que se quiera exponer al front, con `service_date`, `departure_time`, `route_snapshot_ref_id`, `capacity`, `status` coherente (p. ej. `scheduled`).
   - **`service_stops`** — una fila por parada y por servicio, con **`scheduled_time`** = hora efectiva en ese día (derivada de `departure_time` / `base_time` + offsets).

3. **Relación con ms-router (nombres de columna del esquema):**
   - En **`expeditions`** y **`services`**: **`route_snapshot_ref_id`** debe ser el UUID de **`route_snapshots.id`** en ms-router (no `routes.id` salvo que el modelo de front lo documente explícitamente; el dominio usa snapshot).
   - En **`expedition_stops`** y **`service_stops`**: **`stop_logical_id`** debe ser el UUID de **`stops.id`** en ms-router, en el **mismo orden lógico** que `route_stops.sequence_order` del snapshot referenciado.

4. **Coherencia temporal:**
   - **`expeditions.days_of_week`**: bitmask documentado en esquema (bit 0 = lunes … bit 6 = domingo). Ejemplo habitual para laborables: **31** (lun–vie). Si generas servicios **solo** esos días, no insertes filas en sábados/domingos salvo que `days_of_week` lo permita.
   - **`expeditions.base_time`**: hora de referencia (TIME) para la primera parada o para el origen; **`expedition_stops.offset_seconds`** incrementales según el recorrido (pueden ser estimados si no hay GTFS: p. ej. +300 s entre paradas).
   - **`services.departure_time`**: alineada con la salida del primer tramo (o con `base_time`).
   - **`service_stops.scheduled_time`**: TIME por parada ese día (consistente con offsets).

5. **UUIDs deterministas (recomendado):** usa **UUID fijos** en los `INSERT` (CHAR(36)) generados con un criterio estable (p. ej. `UUID v5` con namespace fijo documentado en comentario SQL, o UUIDs fijos listados al inicio del script) para que la migración sea **reproducible** y el **rollback** por script sea posible.

6. **Alcance de rutas:** como mínimo, cubre **1–3 rutas** reales del entorno de desarrollo. **Fuente de UUIDs:** lee `datos/mappings/bcn_import_latest.json` del repositorio (`routes` → `routes.id`, `snapshots` → `route_snapshots.id`, `stops` y `route_stops` para orden y `stop_id`). Si el mapping no está disponible, documenta placeholders y un comentario `TODO` para sustituir UUIDs tras ejecutar el seed de ms-router.

7. **Contenido del fichero SQL:**
   - `USE \`ms-planifications\`;`
   - `SET NAMES utf8mb4;`
   - Comentario de cabecera: propósito, dependencia previa (**Flyway ms-router con datos**, p. ej. `V2__bcn_routes_stops_seed.sql`), rango de fechas abril 2026.
   - **Orden de INSERT** respetando FKs internas: `expeditions` → `expedition_stops` → `planifications` → `services` → `service_stops`.
   - Opcional (no obligatorio para el front mínimo): `capacity_rules`, `plan_generation_jobs` en estado `completed` si quieres simular un job; **no es requisito** si materializas `services` directamente.

8. **Idempotencia / re-ejecución:** o bien la migración es **solo INSERT** en versión nueva Flyway (típico), o documenta que en dev se debe **baseline/repair** si se repite. Evita `INSERT` duplicados sin clave natural: si usas UUID fijos, un segundo `migrate` fallará (aceptable en Flyway).

9. **Verificación sugerida** (como comentario al final del SQL o en README corto):
   - `SELECT COUNT(*) FROM services WHERE service_date BETWEEN '2026-04-01' AND '2026-04-30';`
   - Comprobar que cada `route_snapshot_ref_id` exista en ms-router (consulta manual cruzada).

10. **No incluyas** datos de vehículo/conductor salvo que el front lo exija (`service_assignments` opcional).

### Contexto del repositorio (debes citarlo en comentarios del SQL o en el mensaje)

- Esquema ms-planifications: `ms-planifications/src/main/resources/db/migration/V1__init_schema.sql` (definiciones de `expeditions`, `expedition_stops`, `planifications`, `services`, `service_stops`).
- Modelo narrativo: `docs/3_Modelo_de_Datos.md` §3.3.
- UUIDs de ejemplo rutas/paradas/snapshots BCN: `datos/mappings/bcn_import_latest.json`.
- Tarea relacionada (opcional): `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` (T9).

### Formato del entregable

- Un único archivo: **`ms-planifications/src/main/resources/db/migration/V2__seed_expeditions_services_april_2026.sql`** (o el siguiente número `V*` libre si ya existe V2).
- SQL válido para **MySQL 8.0**, sin extensiones propietarias innecesarias.
- Tono: comentarios claros en español o inglés; sin prosa larga en el cuerpo del SQL.

---

## Notas para quien mantenga este fichero

- Ejecutar migraciones: `docker compose run --rm flyway-planifications` (con MySQL y `.env` del monorepo).
- El front que consuma horarios debe apuntar a endpoints que lean **`services` / `service_stops`** (ms-planifications) o a un BFF que los agregue; ms-router por sí solo no guarda calendario de abril.
- Si cambia el seed de ms-router (UUIDs), hay que **regenerar o actualizar** los UUIDs embebidos en esta migración o externalizarlos a un script generador.
