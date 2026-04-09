# Route Searcher — frontend (estructura base)

Proyecto Vue 3 + Vite + TypeScript. La **vista principal** es el **buscador de rutas** (`/` y `/busqueda` → `SearchView`). Mejoras descritas en `docs/prompt_mejoras_vista_buscador.md` y seguimiento en `docs/6_tareas_tecnicas_front.md`.

## Requisitos

- Node.js 18+ (recomendado 20 LTS)
- npm 9+

## Instalación

```bash
cd frontend
cp .env.example .env
npm install
```

## Scripts

| Comando        | Descripción                |
|----------------|----------------------------|
| `npm run dev`  | Servidor de desarrollo     |
| `npm run build`| Typecheck + build producción |
| `npm run preview` | Vista previa del build  |
| `npm run lint` | ESLint                     |
| `npm run format` | Prettier (src/)        |
| `npm run test` | Vitest                     |

## Variables de entorno (`VITE_*`)

| Variable               | Descripción |
|------------------------|-------------|
| `VITE_API_BASE_URL`    | Base URL del gateway/BFF (ej. `https://host/api/v1`). Sin barra final. |
| `VITE_API_PLANIFICATIONS_BASE_URL` | Base del API de **ms-planifications** (horarios por snapshot y fecha). Ej. `http://localhost:8002/api/v1` o `/dev-ms-planifications/api/v1`. Si se omite, la lista muestra `—` en salida/llegada. |
| `VITE_USE_MOCK_API`    | `true` → las funciones en `src/services/routeSearcherApi.ts` devuelven datos mínimos sin llamar al backend. |
| `VITE_GOOGLE_MAPS_API_KEY` | Clave de la API JavaScript de Google Maps. Necesaria para **mapa** (`GoogleMapContainer`) y **autocompletado de direcciones** (`GooglePlacesInput`). En Google Cloud Console habilita *Maps JavaScript API* y *Places API*; restringe la clave por referrer (p. ej. `http://localhost:5173/*`). |

Copia `.env.example` a `.env` y ajusta.

Sin `VITE_GOOGLE_MAPS_API_KEY`, el mapa muestra un mensaje informativo y los campos de dirección con Google quedan deshabilitados hasta configurar la clave.

## Conexión con ms-router (datos reales)

1. Levanta MySQL + Flyway + **ms-router** (raíz del monorepo: `docker compose up -d` o al menos `mysql`, `flyway-router`, `ms-router`). Puerto por defecto del API: **8001**.
2. En `frontend/.env`: `VITE_USE_MOCK_API=false` y `VITE_API_BASE_URL=http://localhost:8001/api/v1` (o `/dev-ms-router/api/v1` usando el proxy de Vite).
3. `npm run dev` y abre la vista de búsqueda: el panel de rutas llama a `POST /api/v1/search/routes` y el itinerario a `GET /api/v1/routes/{uuid}/schedules`.

Inventario de endpoints vs documentación: `docs/api_inventario_brecha.md`. Tareas de integración: `docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md`.

### Horarios en tarjeta e itinerario (ms-planifications)

1. Levanta también **ms-planifications** (puerto típico **8002**) con migraciones y seed abril 2026 (`V2__seed_expeditions_services_april_2026.sql`).
2. En `frontend/.env`: `VITE_API_PLANIFICATIONS_BASE_URL=http://localhost:8002/api/v1` (o `/dev-ms-planifications/api/v1` con proxy).
3. En la vista de búsqueda, elige fecha **2026-04-15** y comprueba salida/llegada en la tarjeta y horas por parada al expandir el itinerario (datos en `services` / `service_stops`).
4. Al cambiar la fecha en el buscador, se vuelven a cargar rutas y horarios del día.

Detalle de tareas: `docs/tareas_tecnicas_horarios_front_rutas_itinerario.md`.

## Proxy de desarrollo (CORS)

En `vite.config.ts` hay proxies opcionales:

- `/dev-ms-router` → `http://localhost:8001` (ms-router)
- `/dev-ms-planifications` → `http://localhost:8002` (ms-planifications)

Con `VITE_API_BASE_URL=/dev-ms-router/api/v1` las peticiones pasan por Vite y evitan problemas de CORS en el navegador. ms-router incluye `config/cors.php` con orígenes abiertos para desarrollo si llamas al puerto 8001 directamente.

## Estructura relevante

- `src/services/` — `apiClient` (axios) y `routeSearcherApi` (firmas alineadas con la API documentada).
- `src/types/` — tipos TypeScript de referencia.
- `src/mocks/` — respuestas mínimas para modo mock.
- `src/adapters/` — mapeos DTO ↔ UI (pendiente).
- `src/views/SearchView.vue` — pantalla de búsqueda (cabecera, barra de filtros, listado con scroll + mapa Google).
- `src/lib/googleMapsLoader.ts` — carga de la API de Google Maps (una sola vez).
- `src/components/layout/`, `src/components/search/` — piezas de la vista buscador.

## Documentación del repositorio

- API de producto: `docs/4_especificacion_api.md`
- Prompt de este scaffold: `docs/prompt_creacion_frontend.md`
- Prompt vista buscador: `docs/prompt_vista_buscador.md`
- Mejoras buscador: `docs/prompt_mejoras_vista_buscador.md`
- Tareas técnicas frontend: `docs/6_tareas_tecnicas_front.md`