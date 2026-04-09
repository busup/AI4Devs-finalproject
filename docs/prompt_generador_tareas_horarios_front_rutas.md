# Prompt: generador de tareas técnicas — horarios en tarjeta de ruta e itinerario (front)

**Uso:** copia el bloque siguiente (desde «Actúa como…» hasta el final) y pégalo en un asistente de IA. El resultado esperado es un **fichero Markdown nuevo** en `docs/` con tareas técnicas ordenadas y verificables.

---

## Bloque a ejecutar

Actúa como **desarrollador senior full-stack** (Vue 3 / TypeScript, APIs REST, Laravel, MySQL). Tu misión es **definir el conjunto de tareas técnicas** para que, **desde el frontend**, las **rutas muestren horas reales** en:

1. **La “caja” / tarjeta de ruta** (resumen del listado de búsqueda): al menos **hora de salida** y **hora de llegada** (o equivalente del contrato de producto), coherentes con la **fecha de viaje** elegida por el usuario.
2. **El itinerario desplegable por ruta**: en **cada parada**, una **hora visible** (paso por esa parada), alineada con el orden `sequence_order` y con los datos persistidos.

### Contexto funcional actual (repositorio)

- El buscador usa **`POST /api/v1/search/routes`** (ms-router) y el listado en **`frontend/src/components/search/RouteListPanel.vue`** hoy muestra **`dep` / `arr` como `—`** cuando no hay horarios en la respuesta de búsqueda.
- El detalle de ruta / paradas viene de **`GET /api/v1/routes/{id}/schedules`** (ms-router), que devuelve paradas con **`scheduled_time`-like** solo si el backend lo rellena; hoy el itinerario puede construirse con **`stopsToItineraryRows`** usando tiempos genéricos o **`—`** si no hay hora por parada.
- Los **horarios operativos por calendario** viven en **`ms-planifications`**: tablas **`services`** (`service_date`, `departure_time`, `route_snapshot_ref_id`) y **`service_stops`** (`scheduled_time`, `stop_logical_id`, `sequence_order`). Referencia: `docs/3_Modelo_de_Datos.md` §3.3 y migración seed **`ms-planifications/.../V2__seed_expeditions_services_april_2026.sql`** (abril 2026 de ejemplo).
- El contrato de tipos del front incluye **`RouteScheduleItem`** (`departureTime`, `arrivalTime`, `duration`, `trackId`) y **`SearchRouteResultItem.schedules`**: `frontend/src/types/api.ts` y `docs/4_especificacion_api.md`.
- **ms-planifications** expone hoy rutas API limitadas (`routes/api.php`: expediciones, planificaciones, parches de servicios); **no** hay necesariamente un endpoint listo que devuelva “servicios del día por ruta” para el buscador — debe quedar explícito en las tareas si hace falta **nuevo endpoint**, **BFF** o **agregación en ms-router** llamando a ms-planifications.

### Requisitos del entregable que debes producir ahora

1. **Crea un nuevo fichero Markdown** en **`docs/`** con nombre del estilo `docs/tareas_tecnicas_horarios_front_rutas_itinerario.md` (o similar).

2. El documento debe listar **tareas técnicas** en orden lógico, cada una con: **objetivo**, **dependencias**, **entradas/salidas**, **criterios de aceptación** verificables, **riesgos/decisiones**.

3. Las tareas deben cubrir **como mínimo**:
   - **Inventario de brecha:** qué campos de hora tiene hoy la respuesta de búsqueda vs qué necesita la UI; qué tablas/consultas en `ms-planifications` aportan esos datos filtrados por **`route_snapshot_ref_id`** (o `route_id` si se mapea) y **`service_date` = fecha del buscador** (`useSearchStore().departureDate` o equivalente).
   - **Contrato API:** alinear o extender `SearchRoutesResponse` / `RouteScheduleItem` con la realidad (UUID `routeId`, `trackId` o sustituto, formato ISO time `HH:mm` o `HH:mm:ss`, zona horaria — documentar si todo es hora local **Europe/Madrid**).
   - **Backend (una o más opciones, documentar pros/contras):**
     - **Opción A:** Nuevo endpoint en **ms-planifications** (p. ej. `GET /api/v1/services` o `GET /api/v1/routes/{snapshotId}/services?date=`) que devuelva servicios del día con **paradas y `scheduled_time`**.
     - **Opción B:** Ampliar **ms-router** (o un **BFF/gateway**) para que **`POST /search/routes`** enriquezca cada resultado consultando ms-planifications con la **fecha del body** y devuelva **`schedules`** rellenados para la tarjeta.
     - **Opción C:** El front llama en **2 pasos** (búsqueda + N peticiones de horarios por ruta/fecha) — documentar límites de rendimiento y caché.
   - **Criterio de selección de servicio:** si hay varios `services` el mismo día para el mismo snapshot (p. ej. múltiples expediciones), definir regla (primero por `departure_time`, capacidad, estado `scheduled`, etc.).
   - **Frontend — tarjeta de ruta:** sustituir `dep`/`arr` placeholder por datos del contrato (primer `RouteScheduleItem` o campos agregados `firstDeparture` / `lastArrival` si se acuerdan).
   - **Frontend — itinerario:** al expandir una ruta, mostrar **hora por parada**; fuente puede ser la misma respuesta enriquecida de búsqueda o un **detalle por fecha** que incluya `service_stops.scheduled_time` cruzado con nombre de parada (ms-router o payload fusionado).
   - **Cliente HTTP:** si hay **dos bases URL** (`VITE_API_ROUTER`, `VITE_API_PLANIFICATIONS`) o **proxy Vite** (`/dev-ms-planifications`), documentar variables `.env` y **CORS**.
   - **Filtros de fecha:** si el usuario cambia la fecha en la barra de búsqueda, **re-fetch** de horarios coherente con esa fecha.
   - **Casos vacíos:** sin servicio para esa fecha → mensaje i18n claro (no horas inventadas).
   - **Pruebas:** manual con datos seed abril 2026; opcional test de mapper TS.

4. **Tono:** técnico, accionable; poco código salvo pseudocódigo o ejemplos de query SQL ilustrativos.

5. Incluir al final **orden de ejecución sugerido** y tabla **resumen (T1, T2, …)**.

### Referencias obligatorias a citar en el documento generado

- `docs/3_Modelo_de_Datos.md` (§3.2 ms-router, §3.3 ms-planifications).
- `docs/4_especificacion_api.md` (búsqueda y horarios).
- `frontend/src/types/api.ts`, `frontend/src/components/search/RouteListPanel.vue`, `frontend/src/composables/useSearchRoutes.ts`.
- `docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md` (estado de conexión actual).
- `docs/api_inventario_brecha.md` (si existe) o inventario actualizado de endpoints.

---

## Notas para quien mantenga este fichero

- Si se implementa un único gateway, actualiza el bloque «Cliente HTTP» para reflejar una sola `VITE_API_BASE_URL`.
- Tras ejecutar el prompt, revisa que las tareas mencionen **`service_date`** alineado con la fecha del buscador, no solo “hoy”.
