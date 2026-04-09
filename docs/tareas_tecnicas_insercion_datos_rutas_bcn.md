# Tareas técnicas — inserción datos rutas y paradas (BCN)

**Fuentes:** `datos/rutasBCN.csv`, `datos/ParadasRutasBCN.csv` (`route_id` = `id` de rutas).  
**Modelo de referencia:** `docs/3_Modelo_de_Datos.md`.  
**Esquema ms-router:** `ms-router/src/main/resources/db/migration/V1__init_schema.sql`.  
**Datos BCN en ms-router (rutas, paradas, geometrías, outbox):** migración Flyway `V2__bcn_routes_stops_seed.sql` (mismo contenido que genera el ETL).  
**Esquema ms-planifications:** `ms-planifications/src/main/resources/db/migration/V1__init_schema.sql`.  
**Contrato API (alineación futura):** `docs/4_especificacion_api.md`, `frontend/src/types/api.ts`.

---

## Carga en base de datos (Flyway + ETL)

| Paso | Acción |
|------|--------|
| 1 | Tras cambiar CSV o reglas de import, regenerar SQL y la migración V2: `python3 scripts/import-bcn-routes/import_bcn_routes.py` (escribe `scripts/import-bcn-routes/out/bcn_ms_router_seed.sql`, `ms-router/.../V2__bcn_routes_stops_seed.sql`, `datos/mappings/bcn_import_latest.json`, informe T1). |
| 2 | Aplicar migraciones ms-router (esquema + datos BCN): `docker compose run --rm flyway-router` **con MySQL ya levantado** (`docker compose up -d mysql` o stack completo). Flyway ejecuta **V1** (tablas) y **V2** (INSERTs de rutas/paradas). |
| 3 | Alternativa sin Flyway: importar solo el SQL generado en `scripts/import-bcn-routes/out/bcn_ms_router_seed.sql` o usar `import_bcn_routes.py --apply` con variables `MYSQL_*` (no duplicar si V2 ya se aplicó en la misma BD). |

**Reglas de idempotencia:** Flyway ejecuta cada versión **una sola vez** por base de datos. Si ya cargaste los mismos datos con `--apply` manual y luego añades V2, puede haber conflicto de claves primarias: vaciar datos del import (`scripts/import-bcn-routes/rollback_bcn_import.py`) o recrear el volumen MySQL en desarrollo. Si **modificas** `V2__...sql` después de haber migrado, Flyway puede rechazar el checksum; en local se puede usar `flyway repair` (imagen `flyway/flyway`) o borrar historial en dev con cuidado.

**Documentación de reglas y mapeo API:** `docs/reglas_normalizacion_paradas_bcn.md`, `docs/adr/0001-route-id-api-mapping.md`.

---

## T1 — Inventario y perfilado de los CSV

| Campo | Valor |
|--------|--------|
| **Objetivo** | Caracterizar calidad, cardinalidad y reglas de negocio implícitas en los ficheros antes de diseñar el ETL. |
| **Dependencias** | Ninguna. |
| **Entradas** | `datos/rutasBCN.csv`, `datos/ParadasRutasBCN.csv`. |
| **Salidas** | Informe breve: `docs/informe_perfilado_csv_bcn.md` (generado por `import_bcn_routes.py --profile-only` o al generar el import). Incluye conteos, huérfanos, distribución `stop_type_id`, coordenadas fuera de España. |
| **Criterios de aceptación** | Histograma o tabla de `stop_type_id` por ruta; decisión documentada sobre filas con coordenadas fuera de España (ej. `route_id` 59798 en CSV) — excluir, flag o import separado. |
| **Riesgos / decisiones** | `polyline_raw` puede llevar comillas internas: validar parser (no asumir CSV “simple”). Orden de paradas: definir si prima `timestamp` / `start_timestamp` frente a orden de aparición en fichero. |

---

## T2 — Registro de mapeo de claves legado → dominio

| Campo | Valor |
|--------|--------|
| **Objetivo** | Definir cómo persistir la correspondencia entre IDs numéricos del origen y UUIDs del sistema (`stops.id`, `routes.id`, `route_snapshots.id`, `route_stops.id`). |
| **Dependencias** | T1. |
| **Entradas** | Resultados T1; esquema actual (CHAR(36) UUID). |
| **Salidas** | Fichero JSON `datos/mappings/bcn_import_latest.json` (generado por el ETL) con `routes`, `stops`, `snapshots`, `route_stops` y `import_tag`. Opcional staging en BD no implementado; la trazabilidad va en `metadata` de `stops` y en el mapping. |
| **Criterios de aceptación** | Para cada `rutasBCN.id` existe exactamente un `routes.id` UUID; para cada parada importable existe un `stops.id` y trazabilidad al `id` CSV de parada. |
| **Riesgos / decisiones** | Paradas con mismas (lat,lng) y distinto `title`: política de deduplicación (un `stop` compartido vs dos filas) documentada. |

---

## T3 — Reglas de normalización de paradas y secuencia

| Campo | Valor |
|--------|--------|
| **Objetivo** | Fijar reglas deterministas para `route_stops.sequence_order`, `dwell_time_s`, `pickup_allowed` / `dropoff_allowed` y exclusión de filas “base” (`stop_type_id = 100`) si no deben mostrarse como parada de pasajeros. |
| **Dependencias** | T1, T2. |
| **Entradas** | Columnas `stop_type_id`, `type_stop`, `timestamp`, `start_timestamp`, `end_timestamp` en `ParadasRutasBCN.csv`. |
| **Salidas** | Matriz documentada en `docs/reglas_normalizacion_paradas_bcn.md` (exclusión `stop_type_id = 100`, orden por timestamp, bbox España, etc.). |
| **Criterios de aceptación** | Para cada `route_id` importado, `sequence_order` es 1..N sin huecos; no hay dos filas con el mismo orden en el mismo snapshot. |
| **Riesgos / decisiones** | `stop_type_id` NULL o `100` repetido: regla de filtrado o inclusión explícita en geometría solamente. |

---

## T4 — Diseño de inserción `stops` (ms-router)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Poblar `stops` con datos plausibles y coherentes con el modelo (`docs/3_Modelo_de_Datos.md` §3.2). |
| **Dependencias** | T2, T3. |
| **Entradas** | Filas de paradas elegibles; columnas `lat`, `lng`, `requested_address` / `known_title` / `title`. |
| **Salidas** | Filas en `stops` con `name` y `address` derivados (prioridad documentada), `lat`/`lon` DECIMAL, `location` POINT SRID 4326 (`ST_GeomFromText`/`ST_Point`), `timezone` = `Europe/Madrid` para ámbito Barcelona salvo excepción T1, `approval_status` = `approved` para datos de import controlado, `metadata` JSON con `legacy_stop_id`, `legacy_route_id` si aplica. |
| **Criterios de aceptación** | `SELECT COUNT(*)` coincide con paradas únicas según política de deduplicación; índice espacial usable; ningún `location` NULL. |
| **Riesgos / decisiones** | `lat`/`lng` inválidos o 0: regla de descarte o corrección manual listada. |

---

## T5 — Diseño de inserción `routes` y `route_snapshots`

| Campo | Valor |
|--------|--------|
| **Objetivo** | Crear `routes` y un `route_snapshots` inicial por ruta importada, con publicación simulada. |
| **Dependencias** | T2, T4. |
| **Entradas** | `rutasBCN.csv` (`id`, `title`); política de fechas. |
| **Salidas** | `routes`: `name` desde `title`, `status` = `approved` (o `draft` si el flujo exige aprobación posterior). `route_snapshots`: `version_number` = 1, `valid_from`/`valid_until` (p. ej. ventana anual o abierta), `published_at` = NOW() o fecha fija de entorno, `total_distance_m` y `estimated_duration_s` **estimados** (decodificar polyline y sumar longitud aproximada + velocidad media asumida, o valores por defecto documentados con TODO). |
| **Criterios de aceptación** | FK `routes` → `route_snapshots`; `routes.current_snapshot_id` actualizado al snapshot creado (orden: insertar snapshot, luego UPDATE `routes`). |
| **Riesgos / decisiones** | Orden circular FK en `V1__init_schema.sql`: respetar el mismo patrón que el esquema (INSERT snapshot, UPDATE `routes.current_snapshot_id`). |

---

## T6 — Inserción `route_stops`

| Campo | Valor |
|--------|--------|
| **Objetivo** | Vincular cada parada normalizada al snapshot con orden y permisos de subida/bajada. |
| **Dependencias** | T3, T4, T5. |
| **Entradas** | `stop_id` UUID, `route_snapshot_id`, reglas T3. |
| **Salidas** | Filas en `route_stops` con `sequence_order`, `dwell_time_s`, `alias` opcional desde CSV, `pickup_allowed`/`dropoff_allowed` según matriz T3. |
| **Criterios de aceptación** | `UNIQUE (route_snapshot_id, sequence_order)`; FKs respetadas; `active` = 1 salvo regla explícita. |

---

## T7 — Inserción `route_geometries` desde `polyline_raw`

| Campo | Valor |
|--------|--------|
| **Objetivo** | Persistir la geometría de la ruta para mapa y consumo UI. |
| **Dependencias** | T5. |
| **Entradas** | `polyline_raw` (encoded polyline Google); formato almacenado en `route_geometries.format` (`polyline` | `geojson` según ENUM `V1__init_schema.sql`). |
| **Salidas** | Una fila mínima por snapshot con `geometry_type` = `full` o `simplified`, `content` LONGTEXT con el string normalizado o GeoJSON si se convierte. |
| **Criterios de aceptación** | Validación de que el polilínea decodifica a ≥ 2 puntos; si falla, registrar ruta en informe de rechazo y no marcar snapshot como listo para mapa. |
| **Riesgos / decisiones** | Tamaño LONGTEXT y caracteres escapados en CSV; preferir pipeline que lea CSV con parser robusto (no SQL manual masivo). |

---

## T8 — Eventos `outbox_events` (ms-router)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Opcional pero recomendado para integridad con otros servicios: publicar eventos coherentes con `docs/3_Modelo_de_Datos.md` §3.2.3. |
| **Dependencias** | T5, T6, T7. |
| **Entradas** | IDs de rutas/snapshots publicados. |
| **Salidas** | Filas en `outbox_events` con `event_type` = `RoutePublished` (y `RouteEstimateUpdated` si se recalculan métricas), `payload` JSON con `route_id`, `snapshot_id`, `version_number`. |
| **Criterios de aceptación** | Esquema `payload` alineado con lo que consuma el worker de outbox (si existe); si no hay consumidor, marcar como “opcional para entorno local”. |

---

## T9 — Fase opcional: ms-planifications (datos “realistas”)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Habilitar pruebas E2E de búsqueda con horarios si el backend las exige (`expeditions`, `expedition_stops`, `planifications`, `services`, …). |
| **Dependencias** | T5, T6, T4 (UUID de stops como `stop_logical_id`). |
| **Entradas** | Horarios de `ParadasRutasBCN.csv` (`timestamp`, etc.); `route_snapshot_ref_id` = UUID del snapshot en ms-router. |
| **Salidas** | Al menos una **expedición** `active` con `days_of_week` bitmask (lunes–viernes si los datos son laborables), `base_time` alineado con la primera salida del día; `expedition_stops` con `offset_seconds` relativos a `base_time`; opcionalmente `planifications` + `services` concretos para fechas de prueba. |
| **Criterios de aceptación** | Referencias `route_snapshot_ref_id` y `stop_logical_id` existen en ms-router; no FK cruzada en BD (solo lógicas), documentar validación manual. |
| **Riesgos / decisiones** | Sin servicio de búsqueda implementado, esta fase puede **posponerse**; el front solo necesita `POST /search/routes` según `SearchRoutesResponse` — documentar dependencia del backend. |

---

## T10 — Pipeline de carga, idempotencia y rollback

| Campo | Valor |
|--------|--------|
| **Objetivo** | Definir cómo re-ejecutar el import sin duplicados ni estados rotos. |
| **Dependencias** | T2–T8. |
| **Entradas** | ETL `scripts/import-bcn-routes/import_bcn_routes.py`; migración `V2__bcn_routes_stops_seed.sql`; Flyway en `docker compose`. |
| **Salidas** | `import_tag` en `metadata` de paradas y en payload outbox; rollback: `scripts/import-bcn-routes/rollback_bcn_import.py` (SQL desde `bcn_import_latest.json`). **Flyway:** V2 solo se aplica una vez; repetir `flyway-router` en la misma BD no reinserta. Para datos nuevos: nueva migración V3+ o rollback + bump de estrategia documentada. |
| **Criterios de aceptación** | No duplicar mezclando `--apply` manual y V2 en la misma BD sin rollback; script de rollback coherente con UUIDs del mapping. |

---

## T11 — Verificación de datos y calidad

| Campo | Valor |
|--------|--------|
| **Objetivo** | Consultas SQL y checklist para validar el resultado. |
| **Dependencias** | T4–T7 (y T9 si aplica). |
| **Entradas** | BBDD poblada. |
| **Salidas** | `scripts/import-bcn-routes/verify.sql` — conteos, secuencias, gaps entre paradas, geometrías polyline, `routes.current_snapshot_id` NOT NULL. |
| **Criterios de aceptación** | Ninguna FK huérfana; informe de rutas con geometría inválida vacío o justificado. |

---

## T12 — Alineación con API y front

| Campo | Valor |
|--------|--------|
| **Objetivo** | Cuando el backend exponga `POST /api/v1/search/routes`, definir mapeo de `routes.id` interno (UUID) a `routeId` numérico del contrato (`SearchRouteResultItem.routeId` en `frontend/src/types/api.ts`) si difieren — o actualizar el contrato/OpenAPI para UUID. |
| **Dependencias** | T5; decisión de equipo backend. |
| **Entradas** | `docs/4_especificacion_api.md`, tipos TS. |
| **Salidas** | Nota de decisión en README o ADR; evitar desajuste silencioso entre import y contrato. |
| **Criterios de aceptación** | Documento de contrato actualizado o tabla de mapeo estable. |

---

## Resumen de orden de ejecución sugerido

1. Regenerar artefactos desde CSV: `python3 scripts/import-bcn-routes/import_bcn_routes.py`.  
2. Levantar MySQL y ejecutar Flyway ms-router: `docker compose run --rm flyway-router` (aplica **V1 + V2**).  
3. Verificar: `scripts/import-bcn-routes/verify.sql` contra la base `ms-router`.

`T9` en paralelo o después de `T6` si hace falta planificación.  
`T12` en cuanto exista contrato de servicio de búsqueda estable (ver ADR citado arriba).

---

*Documento generado para cumplir el prompt definido en `docs/prompt_generador_tareas_insercion_rutas_paradas_bcn.md`.*
