# Inventario API — documentación (`docs/4`) vs implementación (abril 2026)

Referencia para **T0** (conexión front–back). Actualizar cuando cambien rutas Laravel.

| Endpoint (docs/4 y tipos front) | Servicio | Estado |
|---------------------------------|----------|--------|
| `POST /api/v1/search/routes` | **ms-router** `SearchRoutesController` | **Implementado** — listado de rutas aprobadas con snapshot publicado (demo sin filtro geoespacial estricto). |
| `GET /api/v1/routes/{id}` | **ms-router** `RouteController::show` | **Implementado** — mismo cuerpo que `.../schedules` (detalle + paradas con nombre/coords + geometrías). |
| `GET /api/v1/routes/{id}/schedules` | **ms-router** `RouteController::schedules` | **Implementado** — paradas enriquecidas con datos de `stops`. |
| `GET /api/v1/routes/snapshots/{id}` | **ms-router** `RouteController::showSnapshot` | **Implementado** — metadatos mínimos del snapshot. |
| `POST /api/v1/schedules/by-snapshots` | **ms-planifications** `ScheduleBySnapshotsController` | **Implementado** — body `{ date, routeSnapshotIds[] }` → un servicio canónico por snapshot (`services` + `service_stops`); usado por el buscador para dep/arr e itinerario por fecha. |
| `POST /api/v1/routes`, publish, stops… | **ms-router** | Según `routes/api.php` (escritura / administración). |
| Gateway único `api.routesearch.busup.org` | — | No incluido en repo; en local se usa ms-router directo (`MS_ROUTER_PORT`, p. ej. 8001). |

**Nota:** `docs/4_especificacion_api.md` describe también **sites**, **bookings**, **users**; no están necesariamente implementados en este monorepo. El buscador puede limitarse a **search + detalle de ruta** en ms-router.

**Horarios por fecha:** `POST /api/v1/search/routes` sigue pudiendo devolver `schedules: []` en ms-router; las horas operativas por **fecha de viaje** salen de **ms-planifications** (`route_snapshot_ref_id` + `service_date`), expuestas vía `POST /api/v1/schedules/by-snapshots`. El front enlaza rutas con snapshots mediante `snapshotId` en la respuesta de búsqueda.
