# Tareas técnicas — horarios en tarjeta de ruta e itinerario (front)

**Objetivo:** que el frontend muestre **horas reales** (salida/llegada en la tarjeta y hora por parada en el itinerario), alineadas con la **fecha de viaje** del buscador y con los datos en **`ms-planifications`** (`services`, `service_stops`) referenciando snapshots y paradas de **`ms-router`**.

**Referencias:** `docs/3_Modelo_de_Datos.md` (§3.2, §3.3), `docs/4_especificacion_api.md`, `frontend/src/types/api.ts`, `frontend/src/components/search/RouteListPanel.vue`, `frontend/src/composables/useSearchRoutes.ts`, `docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md`, `docs/api_inventario_brecha.md`.

---

## T0 — Inventario de brecha (datos y API)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Documentar qué expone hoy **`POST /api/v1/search/routes`** (ms-router) respecto a horarios: `SearchRouteResultItem.schedules` suele ir **vacío**; la tarjeta usa **`dep`/`arr` = `—`**. El detalle **`GET /api/v1/routes/{id}/schedules`** (ms-router) aporta paradas y geometría, **no** el calendario operativo de abril. |
| **Dependencias** | Ninguna. |
| **Entradas** | Respuestas reales de los endpoints; esquema `ms-planifications` (`services`, `service_stops`). |
| **Salidas** | Tabla breve: campo UI → origen deseado (`service_stops.scheduled_time`, `services.departure_time`, …) → endpoint actual (sí/no). |
| **Criterios de aceptación** | Queda explícito que las horas por **fecha** vienen de **ms-planifications** filtradas por **`route_snapshot_ref_id`** + **`service_date` = fecha del buscador** (`useSearchStore().departureDate`, formato `YYYY-MM-DD`). |
| **Riesgos** | Confundir `routes.id` con `route_snapshots.id`; el enlace operativo es **snapshot** en `services.route_snapshot_ref_id`. |

---

## T1 — Contrato API y tipos TypeScript

| Campo | Valor |
|--------|--------|
| **Objetivo** | Alinear **`RouteScheduleItem`** y la respuesta de búsqueda con payloads reales: UUIDs como string donde aplique; **`trackId`** sustituido o mapeado a **`service.id`** (ms-planifications) si el producto lo acepta; tiempos en **`HH:mm`** o **`HH:mm:ss`** y documentar zona **Europe/Madrid** (solo display, sin cambiar columnas TIME en BD). |
| **Dependencias** | T0. |
| **Entradas** | `frontend/src/types/api.ts`, `docs/4_especificacion_api.md`. |
| **Salidas** | Tipos actualizados o tipos nuevos (`ServiceStopSchedule`, etc.) + nota en ADR o comentario en `api.ts`. |
| **Criterios de aceptación** | Ningún campo obligatorio sin significado; `schedules[]` puede contener al menos un elemento cuando exista servicio para la fecha. |
| **Riesgos** | Contrato legacy con `trackId: number`; mantener compatibilidad o ampliar a `string \| number`. |

---

## T2 — Decisión de backend: una opción (documentar pros/contras)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Elegir e implementar **una** estrategia de obtención de horarios (o fase 1 + fase 2). |
| **Dependencias** | T0, T1. |
| **Opciones** | **A)** Nuevo endpoint en **ms-planifications** (p. ej. `GET /api/v1/route-snapshots/{snapshotId}/services?date=YYYY-MM-DD` o lista de servicios con `service_stops` anidadas). **B)** **ms-router** o **BFF** enriquece `POST /search/routes` llamando a ms-planifications con la fecha del body. **C)** Front: `POST /search/routes` + **N** llamadas por ruta/fecha (documentar límite N, loading y caché en memoria por `(routeId, date)`). |
| **Salidas** | ADR corto o sección en README: decisión, URL base, auth si aplica. |
| **Criterios de aceptación** | Una sola fuente de verdad acordada para “horario del día D para snapshot S”. |
| **Riesgos** | Latencia en opción C; acoplamiento en B si ms-router llama HTTP a otro servicio (timeouts, fallbacks). |

---

## T3 — Backend: consulta y regla de selección de servicio

| Campo | Valor |
|--------|--------|
| **Objetivo** | Definir consulta SQL/Eloquent que obtenga **`services`** con `service_date = :date` y `route_snapshot_ref_id = :snapshotId`, y **`service_stops`** ordenados por `sequence_order`. Si hay **varios** servicios el mismo día para el mismo snapshot, regla explícita: p. ej. **`ORDER BY departure_time ASC, id ASC LIMIT 1`** y estado `scheduled` preferido. |
| **Dependencias** | T2 (endpoint o capa que implemente). |
| **Entradas** | Tablas `services`, `service_stops`; seed `V2__seed_expeditions_services_april_2026.sql` para pruebas en abril 2026. |
| **Salidas** | Handler + Resource JSON con `departureTime`, tiempos por parada, `duration` calculable (última parada − primera). |
| **Criterios de aceptación** | Misma fecha en body de búsqueda y en query; sin mezclar zonas horarias en servidor más allá de TIME almacenado. |
| **Riesgos** | Datos duplicados por múltiples `planifications`; documentar si se filtra por `planification_id` o se asume un servicio canónico. |

---

## T4 — CORS, env y red (dos APIs o gateway)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Si el front llama a **ms-planifications** además de **ms-router**: variables `VITE_API_ROUTER` / `VITE_API_PLANIFICATIONS` o proxy Vite (`/dev-ms-planifications` → puerto 8002) y **CORS** en Laravel (`config/cors.php` o equivalente en ms-planifications). |
| **Dependencias** | T2. |
| **Entradas** | `docker-compose.yml` puertos, `frontend/vite.config.ts` proxies. |
| **Salidas** | `.env.example` documentado; prueba manual desde navegador sin error de preflight. |
| **Criterios de aceptación** | Peticiones OPTIONS y GET/POST exitosas en local. |
| **Riesgos** | Producción debería unificar gateway; documentar diferencia local vs prod. |

---

## T5 — Frontend: cliente y mappers de horario

| Campo | Valor |
|--------|--------|
| **Objetivo** | Añadir funciones en `src/services/` (o extender `routeSearcherApi.ts`) para obtener horarios por **`routeId`/`snapshotId` + `date`**, y mapear a **`RouteScheduleItem`** + lista de paradas con hora para el itinerario. |
| **Dependencias** | T1, T3, T4. |
| **Entradas** | Tipos nuevos/actualizados, respuesta JSON del backend. |
| **Salidas** | Módulo reutilizable (p. ej. `getRouteDaySchedule(snapshotId, date)`), sin duplicar URLs. |
| **Criterios de aceptación** | Formato de hora consistente en tarjeta e itinerario (misma función `formatTime` i18n). |
| **Riesgos** | Cachear por `(snapshotId, date)` para no repetir N veces al expandir tarjetas. |

---

## T6 — Frontend: tarjeta de ruta (`RouteListPanel`)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Sustituir **`dep`/`arr`** placeholder por **salida y llegada** del servicio elegido para la **fecha del store** (primer y último `scheduled_time` o campos `departureTime`/`arrivalTime` del contrato). |
| **Dependencias** | T5. |
| **Entradas** | `RouteListPanel.vue`, `useSearchStore().departureDate`. |
| **Salidas** | UI con horas legibles; estados loading/error por fila si se carga en lazy. |
| **Criterios de aceptación** | Coherencia con la misma fecha que la barra de búsqueda; si no hay servicio, mostrar `—` o texto i18n “Sin horario este día”. |
| **Riesgos** | Evitar layout shift; opcional skeleton en la columna de horas. |

---

## T7 — Frontend: itinerario con hora por parada

| Campo | Valor |
|--------|--------|
| **Objetivo** | En el bloque desplegable del itinerario, mostrar **`scheduled_time`** (o equivalente) junto a cada parada; orden = **`sequence_order`**. Reutilizar datos ya obtenidos para la tarjeta o cargar al expandir si se eligió lazy loading. |
| **Dependencias** | T5, T6. |
| **Entradas** | `stopsToItineraryRows` evolucionado o sustituido para aceptar `{ time, place, … }` con hora real. |
| **Salidas** | Lista legible; segmentos “En ruta” opcionales con duración estimada entre horas. |
| **Criterios de aceptación** | Las horas coinciden con filas de `service_stops` en BD para la fecha de prueba (abril 2026 con seed). |
| **Riesgos** | Paradas solo en ms-router sin fila en `service_stops` para ese servicio → no inventar hora. |

---

## T8 — Re-fetch al cambiar fecha y vacíos

| Campo | Valor |
|--------|--------|
| **Objetivo** | Al cambiar **`departureDate`** en el store, invalidar caché de horarios y **volver a cargar** búsqueda o solo los horarios según diseño (documentar cuál). |
| **Dependencias** | T5–T7. |
| **Entradas** | `SearchBookingBar` / flujo que actualiza la fecha. |
| **Salidas** | Comportamiento predecible; strings i18n para “sin servicios este día”. |
| **Criterios de aceptación** | Fecha fuera del rango del seed (p. ej. sin filas en `services`) → mensaje claro, sin crash. |
| **Riesgos** | Doble request si búsqueda y horarios no están unificados. |

---

## T9 — Verificación manual y pruebas opcionales

| Campo | Valor |
|--------|--------|
| **Objetivo** | Checklist: Docker up → migraciones ms-router + ms-planifications (seed abril 2026) → front con envs correctos → fecha **2026-04-15** → verificar horas en tarjeta e itinerario contra BD (`SELECT … FROM services JOIN service_stops …`). |
| **Dependencias** | T2–T8. |
| **Salidas** | Pasos en `frontend/README.md` o sección corta en este doc. |
| **Criterios de aceptación** | Otro dev reproduce en &lt; 20 minutos. |
| **Opcional** | Test unitario del mapper de tiempos (entrada JSON → `RouteScheduleItem`). |

---

## Orden de ejecución sugerido

`T0 → T1 → T2 → T3 → T4 → T5 → T6 → T7 → T8 → T9`

---

## Tabla resumen

| Id | Tema |
|----|------|
| T0 | Inventario brecha datos/API vs UI |
| T1 | Contrato y tipos TS |
| T2 | Elección estrategia backend (A / B / C) |
| T3 | Query + regla si hay varios servicios |
| T4 | CORS, env, proxy dos APIs |
| T5 | Cliente + mappers horarios |
| T6 | Tarjeta: dep/arr reales |
| T7 | Itinerario: hora por parada |
| T8 | Re-fetch por fecha + vacíos |
| T9 | Verificación manual (+ tests opcionales) |

---

*Documento generado a partir de `docs/prompt_generador_tareas_horarios_front_rutas.md`.*

---

## Estado de implementación (abril 2026)

| Id | Estado | Notas |
|----|--------|--------|
| T0 | Hecho | Fila y nota en `docs/api_inventario_brecha.md`. |
| T1 | Hecho | Tipos en `frontend/src/types/api.ts` (`RouteDaySchedule`, `snapshotId`, `trackId` ampliable). |
| T2 | Hecho | **Opción A + batch:** `POST /api/v1/schedules/by-snapshots` en ms-planifications (varios snapshots en una petición). |
| T3 | Hecho | `ScheduleBySnapshotsController`: preferencia `scheduled`, `ORDER BY departure_time`, `id`; duración = última parada − primera. |
| T4 | Hecho | `config/cors.php` en ms-planifications; proxy `/dev-ms-planifications` en Vite; `VITE_API_PLANIFICATIONS_BASE_URL`. |
| T5 | Hecho | `frontend/src/services/planificationsApi.ts` (`fetchSchedulesBySnapshots`). |
| T6 | Hecho | `RouteListPanel.vue` muestra dep/arr reales; mensaje i18n si no hay servicio el día. |
| T7 | Hecho | `stopsToItineraryRows` con `scheduledTime` por `stop_logical_id`. |
| T8 | Hecho | `watch(departureDate)` en `useSearchRoutes` + limpieza de itinerario en el panel. |
| T9 | Hecho | Checklist breve en `frontend/README.md`. |
