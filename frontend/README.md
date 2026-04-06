# Route Searcher — frontend (estructura base)

Proyecto Vue 3 + Vite + TypeScript según `docs/prompt_creacion_frontend.md`: **scaffold** listo para añadir vistas y lógica de producto en fases posteriores.

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
| `VITE_USE_MOCK_API`    | `true` → las funciones en `src/services/routeSearcherApi.ts` devuelven datos mínimos sin llamar al backend. |

Copia `.env.example` a `.env` y ajusta.

## Proxy de desarrollo (CORS)

En `vite.config.ts` hay proxies opcionales:

- `/dev-ms-router` → `http://localhost:8001` (ms-router)
- `/dev-ms-planifications` → `http://localhost:8002` (ms-planifications)

Úsalos solo si el cliente apunta a esas rutas relativas; el contrato documentado en `docs/4_especificacion_api.md` asume un **gateway** unificado (`VITE_API_BASE_URL`).

## Estructura relevante

- `src/services/` — `apiClient` (axios) y `routeSearcherApi` (firmas alineadas con la API documentada).
- `src/types/` — tipos TypeScript de referencia.
- `src/mocks/` — respuestas mínimas para modo mock.
- `src/adapters/` — mapeos DTO ↔ UI (pendiente).
- `src/views/` — vacío (siguiente fase).

## Documentación del repositorio

- API de producto: `docs/4_especificacion_api.md`
- Prompt de este scaffold: `docs/prompt_creacion_frontend.md`
