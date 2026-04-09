# Informe de perfilado CSV (T1) — rutas BCN

- **Rutas en `rutasBCN.csv`:** 17
- **Filas en `ParadasRutasBCN.csv`:** 89
- **`route_id` en paradas sin ruta en CSV de rutas:** 0 → []
- **Rutas sin ninguna fila de paradas:** 0 → []

## Distribución `stop_type_id` (todas las filas)

| Valor | Filas |
|-------|-------|
| 1 | 17 |
| 100 | 34 |
| 2 | 21 |
| 3 | 17 |

## Coordenadas fuera del bbox España (~import)

- Filas con lat/lng fuera de [36–44] × [-10–5]: **13**

| legacy_stop_id | route_id | lat | lng |
|----------------|----------|-----|-----|
| 739079 | 59798 | 29.915258850004 | -95.206242092048 |
| 739080 | 59798 | 29.916767563379 | -95.207734142707 |
| 739081 | 59798 | 30.000880345701 | -95.58354160619 |
| 739082 | 59798 | 29.973961492397 | -95.697993501299 |
| 739083 | 59798 | 29.970061831059 | -95.702889871178 |
| 739084 | 59799 | 29.916817563379 | -95.207684142707 |
| 739085 | 59799 | 29.916767563379 | -95.207734142707 |
| 739086 | 59799 | 29.786062773387 | -95.592815680401 |
| 739087 | 59799 | 29.786112773387 | -95.592765680401 |
| 739670 | 59845 | 29.786062773387 | -95.592815680401 |
| 739671 | 59845 | 29.916767563379 | -95.207734142707 |
| 739672 | 59845 | 29.786112773387 | -95.592765680401 |
| 739673 | 59845 | 29.970061831059 | -95.702889871178 |

### Decisión

Se **excluyen** filas de paradas fuera del bbox para el seed ms-router. Rutas que queden sin paradas válidas (tras excluir `stop_type_id=100` y fuera de bbox) **no se importan**.

### Encoding

Ficheros leídos como **UTF-8**. Parser: `csv.DictReader` (respeta comillas y polilíneas largas).
