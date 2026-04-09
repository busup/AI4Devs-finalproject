# Reglas de normalización — paradas BCN (import CSV)

| `stop_type_id` (CSV) | Tratamiento | `pickup_allowed` | `dropoff_allowed` | Notas |
|---------------------|-------------|------------------|-------------------|--------|
| `100` | **Excluido** de `route_stops` | — | — | Nodo logístico “Base”; la geometría de la ruta viene del `polyline_raw`, no de estas filas. |
| `1`, `2`, `3` u otro distinto de `100` | Incluido | `1` si tipo implica recogida (1 o 2); `1` si bajada (2 o 3). Por defecto ambos `1` si ambiguo. | Igual que pickup salvo reglas explícitas. | Orden por `timestamp` ascendente (parseo `YYYY-MM-DD HH:MM:SS`). |
| NULL | Excluido | — | — | Fila inválida. |

**`dwell_time_s`:** si `start_timestamp` y `end_timestamp` están presentes y son parseables, `max(0, (end - start).seconds)`; si no, `0`.

**Coordenadas fuera de ámbito import (España peninsular aprox.):** `lat ∈ [36, 44]`, `lon ∈ [-10, 5]`. Filas fuera se **excluyen**; si una ruta queda sin paradas válidas, la ruta **no se importa** (se lista en el informe).

**Deduplicación de `stops`:** un registro `stops` por **fila** de `ParadasRutasBCN.csv` (clave legado = `id` de fila de parada), aunque `(lat,lng)` coincidan entre filas — simplifica trazabilidad `legacy_stop_id`.
