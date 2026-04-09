# Tareas técnicas — conexión front ↔ back (rutas y paradas desde BBDD)

**Objetivo:** que el frontend consuma **datos reales** de **rutas** y **paradas** persistidos en **ms-router** (y horarios en **ms-planifications** si el contrato lo exige), alineado con `docs/4_especificacion_api.md` y `frontend/src/types/api.ts`.

**Generado para:** ejecutar el trabajo descrito en `docs/prompt_generador_tareas_conexion_front_back_rutas.md`.

---

## T0 — Inventario de brecha (API documentada vs código)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Documentar qué existe hoy en `ms-router/routes/api.php` (y ms-planifications) frente a `POST /api/v1/search/routes` y flujos del buscador en `docs/4_especificacion_api.md`. |
| **Dependencias** | Ninguna. |
| **Entradas** | `ms-router/routes/api.php`, `ms-planifications/routes/api.php` (si existe), `docs/4_especificacion_api.md`. |
| **Salidas** | Tabla breve en wiki o comentario en issue: endpoint → servicio → estado (implementado / parcial / ausente). |
| **Criterios de aceptación** | Lista explícita: p. ej. búsqueda geoespacial documentada en docs/4 **no** expuesta como `POST .../search/routes` en ms-router hasta que se implemente (o se documente un BFF). |
| **Riesgos** | Confundir documentación aspiracional con rutas reales; mitigar con este inventario antes de codificar el front. |

---

## T1 — Decisión de contrato: IDs y forma de las respuestas

| Campo | Valor |
|--------|--------|
| **Objetivo** | Fijar cómo el front recibe `routeId` y `stopId` (UUID en BBDD vs enteros en `frontend/src/types/api.ts`). |
| **Dependencias** | T0. |
| **Entradas** | `docs/adr/0001-route-id-api-mapping.md`, tipos en `frontend/src/types/api.ts`. |
| **Salidas** | Decisión registrada (actualizar ADR o README corto): p. ej. API devuelve UUID como string y el front amplía tipos; o API expone entero mapeado desde tabla/columna legacy. |
| **Criterios de aceptación** | Un solo criterio por capa: sin conversiones silenciosas sin tests; tipos TS actualizados o capa `mappers/` documentada. |

---

## T2 — Backend ms-router: lectura de ruta + paradas + geometría

| Campo | Valor |
|--------|--------|
| **Objetivo** | Exponer al menos un endpoint **GET** que, dado un identificador de ruta (UUID o el que se acuerde en T1), devuelva **nombre de ruta**, **snapshot actual**, **lista ordenada de paradas** (`sequence_order`, nombre, lat/lng, alias) y opcionalmente **polilínea** desde `route_geometries`. |
| **Dependencias** | T1, datos en BBDD (seed/migración BCN u otros). |
| **Entradas** | Tablas `routes`, `route_snapshots`, `route_stops`, `stops`, `route_geometries`. |
| **Salidas** | Ruta Laravel + Resource/DTO JSON versionado; tests de feature mínimos o prueba manual documentada. |
| **Criterios de aceptación** | Respuesta coherente con filas existentes; orden de paradas = `sequence_order` ascendente; sin N+1 queries inaceptables (eager load o join). |
| **Riesgos** | Exponer UUIDs internos si el contrato público exige otro formato; resolver en T1. |

---

## T3 — Backend: búsqueda o listado para el buscador (alcance mínimo)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Permitir al front **obtener un conjunto de rutas** relevantes para la UI (listado paginado, o `POST /search/routes` simplificado con origen/destino que filtre por bounding box o por proximidad a paradas). |
| **Dependencias** | T2 (reutilizar consultas a stops/routes). |
| **Entradas** | `docs/4_especificacion_api.md` (POST search), coordenadas de prueba. |
| **Salidas** | Endpoint acordado (mismo path que docs/4 o prefijo bajo ms-router) + cuerpo JSON estable. |
| **Criterios de aceptación** | Con datos BCN cargados, el front puede mostrar **al menos una ruta** con **paradas** sin mocks; criterio de relevancia documentado (p. ej. “rutas con alguna parada en radio R km”). |
| **Riesgos** | Complejidad geoespacial: empezar por filtro bbox o “todas las rutas aprobadas” en entorno demo si el plazo es corto. |

---

## T4 — Backend ms-planifications (opcional): horarios y `schedules`

| Campo | Valor |
|--------|--------|
| **Objetivo** | Si la UI muestra `schedules` / `trackId` como en `SearchRouteResultItem`, poblar o simular datos y exponer `GET /api/v1/routes/{id}/schedules` coherente con `docs/4`. |
| **Dependencias** | T1, snapshot UUID existente en ms-router. |
| **Entradas** | Esquema ms-planifications, `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` (T9 opcional). |
| **Salidas** | Respuesta JSON con estructura que el front pueda tipar; o decisión explícita de **mock temporal** de horarios con aviso en UI. |
| **Criterios de aceptación** | Contrato documentado vs implementación; sin campos obligatorios vacíos sin significado. |

---

## T5 — Front: cliente API, env y CORS

| Campo | Valor |
|--------|--------|
| **Objetivo** | Configurar **base URL** del API (`VITE_*`), proxy en Vite si hace falta, y asegurar **CORS** (o mismo origen vía proxy) entre front y ms-router en local/Docker. |
| **Dependencias** | T2 o T3 (URL conocida). |
| **Entradas** | `frontend/.env.example`, `docker-compose.yml` puertos (`MS_ROUTER_PORT`). |
| **Salidas** | `.env` documentado; peticiones `fetch/axios` exitosas desde el navegador sin error de red preflight. |
| **Criterios de aceptación** | Llamada de prueba desde la app (o consola) a un endpoint real devuelve 200 con JSON. |

---

## T6 — Front: capa de datos (store/composable) y mapeo a tipos

| Campo | Valor |
|--------|--------|
| **Objetivo** | Centralizar llamadas al back (búsqueda + detalle de ruta/paradas) y mapear respuestas a tipos en `frontend/src/types/api.ts` o a tipos internos ampliados. |
| **Dependencias** | T1, T3, T5. |
| **Entradas** | Componentes de búsqueda existentes en `frontend/src`. |
| **Salidas** | Módulo único (p. ej. `useRouteSearch` / store Pinia) sin duplicar URLs; estados loading/error. |
| **Criterios de aceptación** | Sustituir datos mock por datos de API en el flujo principal del buscador; tests unitarios opcionales del mapper. |

---

## T7 — Front: UI de rutas y paradas

| Campo | Valor |
|--------|--------|
| **Objetivo** | Mostrar **título de ruta**, lista de **paradas en orden**, y opcionalmente **mapa** con puntos y polilínea si T2 devuelve geometría. |
| **Dependencias** | T6, T2. |
| **Entradas** | Vistas del buscador (`SearchView` o equivalente). |
| **Salidas** | UI alineada con diseño existente; textos de vacío/error claros. |
| **Criterios de aceptación** | Orden de paradas coincide con `sequence_order`; coordenadas visibles o en mapa. |

---

## T8 — Verificación integral y documentación

| Campo | Valor |
|--------|--------|
| **Objetivo** | Checklist manual: Docker up → migraciones/seed → abrir front → búsqueda → ver rutas/paradas desde BBDD. |
| **Dependencias** | T2–T7. |
| **Entradas** | `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` (datos cargados). |
| **Salidas** | Pasos en README del front o sección corta en `readme.md` del monorepo. |
| **Criterios de aceptación** | Otro desarrollador reproduce el flujo en &lt; 15 minutos con los comandos indicados. |

---

## Resumen de orden sugerido

`T0 → T1 → T2 → T5 → T3 → T6 → T7 → T4 (si aplica horarios) → T8`

---

## Tabla resumen

| Id | Tema |
|----|------|
| T0 | Inventario brecha API docs vs código |
| T1 | Contrato IDs (UUID vs numérico) y mapeo |
| T2 | GET ruta + paradas + geometría (ms-router) |
| T3 | Búsqueda/listado para el buscador |
| T4 | Horarios (ms-planifications) — opcional |
| T5 | Front: env, CORS, proxy |
| T6 | Store/composable + mappers |
| T7 | UI rutas y paradas (+ mapa) |
| T8 | Verificación y doc de arranque |

---

*Documento alineado con `docs/prompt_generador_tareas_conexion_front_back_rutas.md`.*

---

## Estado de implementación (2026-04)

| Tarea | Estado | Notas |
|-------|--------|--------|
| T0 | Hecho | `docs/api_inventario_brecha.md` |
| T1 | Hecho | Tipos `routeId` / `stopId` como `string \| number`; ADR actualizado |
| T2 | Hecho | `GET /api/v1/routes/{id}` = mismo cuerpo que `.../schedules`; paradas con nombre y coordenadas |
| T3 | Hecho | `POST /api/v1/search/routes` en ms-router |
| T4 | Pendiente | Horarios vacíos en respuesta de búsqueda; `schedules` en detalle según handler existente |
| T5 | Hecho | `ms-router/config/cors.php`; `frontend/.env.example` y README |
| T6 | Hecho | `src/composables/useSearchRoutes.ts`, `routeSearcherApi.ts` |
| T7 | Hecho | `RouteListPanel.vue` consume API; itinerario vía `getRouteDetail` |
| T8 | Hecho | Sección en `frontend/README.md` |
