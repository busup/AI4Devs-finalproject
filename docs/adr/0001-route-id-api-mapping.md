# ADR 0001: Identificadores de ruta en API vs ms-router

## Estado

Aceptado (provisional hasta que el servicio de búsqueda esté implementado).

## Contexto

- En **ms-router**, `routes.id` es **UUID** (`CHAR(36)`).
- En **`frontend/src/types/api.ts`**, `SearchRouteResultItem.routeId` está tipado como **`number`** (int32), alineado con ejemplos en `docs/4_especificacion_api.md`.

## Decisión

1. El **import BCN** conserva UUID en base de datos; no fuerza enteros.
2. La capa **API/BFF** debe mapear:
   - **Opción A (recomendada):** cambiar el contrato OpenAPI/TS a `routeId: string` (UUID), o
   - **Opción B:** mantener `number` y exponer una tabla o función de mapeo estable `numeric_id ↔ routes.id` solo en el servicio de búsqueda.

3. Hasta que exista ese servicio, el front puede seguir usando **mocks** o ampliar tipos localmente sin bloquear el ETL.

## Consecuencias

- Scripts de import no generan `routeId` numérico.
- Cualquier cliente que asuma entero debe actualizarse cuando el backend fije el contrato.

## Seguimiento (2026-04)

- **ms-router** expone `routeId` y `stopId` como **strings UUID** en `POST /api/v1/search/routes` y en el detalle de ruta.
- En **front**, `SearchRouteResultItem.routeId` y `AssignedStop.stopId` se tipan como `string | number` para compatibilidad con el contrato documentado hasta unificar en string (UUID).
