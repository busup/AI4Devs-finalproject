# Prompt: generador de tareas técnicas — inserción BBDD rutas y paradas (BCN)

**Uso:** copia el bloque siguiente (desde «Actúa como…» hasta el final) y pégalo en un asistente de IA o entrégalo a un desarrollador. **No** sustituye la ejecución del ETL ni las migraciones; solo define el trabajo a realizar.

---

## Bloque a ejecutar

Actúa como **programador senior con experiencia en bases de datos relacionales**, diseño de datos y entornos **MySQL 8** / microservicios. Tu misión es **definir el conjunto de tareas técnicas** necesarias para poder **insertar en base de datos** los datos de **rutas** y **paradas** a partir de los ficheros CSV del repositorio, respetando el modelo de dominio del proyecto y rellenando tablas relacionadas con **datos plausibles y coherentes** (no placeholders vacíos donde haga falta integridad operativa).

### Fuentes de datos (obligatorias)

1. **Rutas:** `datos/rutasBCN.csv`  
   - Columnas observadas: `id`, `title`, `polyline_raw` (polilínea codificada; el contenido puede incluir comillas y formato complejo CSV).

2. **Paradas por ruta:** `datos/ParadasRutasBCN.csv`  
   - Columnas observadas (entre otras): `id`, `route_id`, `stop_type_id`, `type_stop`, `province_id`, `external_stop_id`, `requested_address`, `known_title`, `title`, `description`, `notes`, `timestamp`, `start_timestamp`, `end_timestamp`, `lat`, `lng`, `is_load`, …  
   - **Regla de unión:** el campo **`route_id`** en el fichero de paradas es el **identificador de ruta** que corresponde al **`id`** de `rutasBCN.csv`.

### Contexto del repositorio (debes leerlo y citarlo en las tareas)

- Modelo de datos y ownership: `docs/3_Modelo_de_Datos.md` (ms-router: `stops`, `routes`, `route_snapshots`, `route_stops`, `route_geometries`, outbox; ms-planifications: expediciones, servicios, etc.).
- Esquema SQL de referencia (Flyway): `ms-router/src/main/resources/db/migration/V1__init_schema.sql` y, si aplica, migraciones de ms-planifications.
- Contrato API orientativo para el front: `docs/4_especificacion_api.md` y `frontend/src/types/api.ts` (para alinear IDs/naming donde el backend exponga búsqueda de rutas).

### Requisitos del entregable que debes producir ahora

1. **Crea un nuevo fichero Markdown** en el directorio **`docs/`** de este repositorio (nombre sugerido: `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` o similar, con fecha en el título si lo ves útil).

2. Ese documento debe listar **tareas técnicas** en orden lógico (descubrimiento → diseño → implementación → verificación), cada una con:
   - **Objetivo** claro.
   - **Dependencias** (qué tareas previas hacen falta).
   - **Entradas/salidas** (tablas, scripts, artefactos).
   - **Criterios de aceptación** verificables.
   - Riesgos o decisiones abiertas (p. ej. mapeo de IDs numéricos del CSV a UUID del sistema, deduplicación de `stops`, orden de `route_stops`).

3. Las tareas deben cubrir **como mínimo**:
   - **Análisis de los CSV:** validación de encoding, separadores, filas corruptas, cardinalidad (rutas sin paradas, paradas huérfanas), orden de paradas por ruta (usar timestamps o secuencia explícita).
   - **Estrategia de claves:** cómo mapear `rutasBCN.id` (numérico) y `ParadasRutasBCN.id` / `route_id` al modelo interno (UUID en ms-router), manteniendo tablas de correspondencia o columnas de importación si aplica.
   - **Inserción ms-router:**
     - `stops` desde coordenadas y textos del CSV (`lat`/`lng`, `name`/`address`, `timezone` creíble para Barcelona, `approval_status`, `metadata` opcional con trazas del import).
     - `routes` y `route_snapshots` (versión inicial, fechas `valid_from`/`valid_until`, `published_at`, métricas `total_distance_m` / `estimated_duration_s` **derivadas o estimadas de forma razonable** si no vienen en el CSV).
     - `route_stops` con `sequence_order`, flags `pickup_allowed` / `dropoff_allowed` alineados con `stop_type_id` o reglas documentadas.
     - `route_geometries` a partir de `polyline_raw` (normalizar formato, GeoJSON/polyline según `ENUM` del esquema).
   - **Tablas relacionadas y datos “realistas”:** definir qué insertar en **outbox** (eventos `RoutePublished`, etc.), y en **ms-planifications** (p. ej. `expeditions` con `route_snapshot_ref_id`, días de la semana, `base_time` coherentes con horarios de paradas) **solo en la medida en que** el proyecto requiera esos datos para pruebas end-to-end; si no es imprescindible, marcarlo como fase opcional con justificación.
   - **Scripts de carga:** SQL (INSERT… SELECT / LOAD DATA), o script Node/Python, o pipeline documentado; incluir **idempotencia** o estrategia de re-ejecución (borrado por lote, transacciones).
   - **Pruebas:** consultas de comprobación (conteos, integridad referencial, `ST_Distance_Sphere` o equivalente para paradas cercanas), y checklist manual en mapa/UI si existe.

4. **Tono:** técnico, preciso, sin relleno; asume un equipo que va a implementar el ETL en el siguiente sprint.

5. **No** incluyas en el documento generado el código completo del importador salvo que sea pseudocódigo o fragmentos ilustrativos; el foco es la **lista de tareas** y decisiones.

---

## Notas para quien mantenga este fichero

- Cuando ejecutes el prompt anterior, el asistente debe **escribir** el fichero `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` (o el nombre acordado) dentro del repo.
- Si el esquema BBDD cambia, actualiza las referencias en el bloque «Contexto del repositorio».
