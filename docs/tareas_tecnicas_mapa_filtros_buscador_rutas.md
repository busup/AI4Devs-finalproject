# Tareas técnicas — mapa con ruta, filtro explícito y badge «Recomendada»

**Objetivo:** al **seleccionar** una ruta, mostrar en el mapa el **trazado** (polyline) y **paradas numeradas**; añadir botón **Filtrar** que aplique criterios de búsqueda; **no** mostrar «Recomendada» por defecto; tras filtrar, listar solo coincidencias y marcar **como máximo una** como recomendada según regla explícita.

**Origen del alcance:** `docs/prompt_generador_tareas_mapa_filtros_buscador_rutas.md`.

**Referencias cruzadas:** `docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md`, `docs/tareas_tecnicas_horarios_front_rutas_itinerario.md`, `docs/api_inventario_brecha.md`, `frontend/src/views/SearchView.vue`, `frontend/src/components/search/RouteListPanel.vue`, `frontend/src/components/search/SearchBookingBar.vue`, `frontend/src/components/search/GoogleMapContainer.vue`, `frontend/src/composables/useSearchRoutes.ts`, `frontend/src/stores/search.ts`, `frontend/src/types/api.ts` (`RouteDetailResponse`, `RouteGeometryDetail`, `RouteStopDetail`), `ms-router` `SearchRoutesHandler`, `GET /api/v1/routes/{id}/schedules`.

---

## T0 — Inventario de brecha (UI vs datos)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Dejar escrito qué hay hoy: listado y mapa **desacoplados**; `RouteListPanel` marca `recommended` en la **primera** ruta siempre; `GoogleMapContainer` no recibe `routeId` ni dibuja geometría; `POST /search/routes` **no filtra** por origen/destino/hora; detalle `.../schedules` sí trae `geometries` + `stops` con coords. |
| **Dependencias** | Ninguna. |
| **Entradas** | Código actual de los componentes y respuestas API reales (seed BCN). |
| **Salidas** | Tabla breve: necesidad UI → fuente de datos → disponible (sí/no/parcial). |
| **Criterios de aceptación** | Queda claro que el filtrado **fase 1** puede ser **solo cliente** sobre resultados + detalle/horarios ya cargados, y que **fase 2** opcional ampliaría `SearchRoutesHandler` o query en POST. |
| **Riesgos** | Filtrar solo en cliente sin prefetch de detalle implica **N llamadas** al expandir o una **precarga** al pulsar Filtrar — documentar la estrategia elegida en T7. |

---

## T1 — Estado compartido: selección de ruta y sincronización listado ↔ mapa

| Campo | Valor |
|--------|--------|
| **Objetivo** | Un único sitio de verdad para **`selectedRouteId`** (string UUID de `routeId`) visible por **RouteListPanel** y **mapa** (directamente o vía `SearchView` + provide/inject). Recomendación: **Pinia** (`useSearchStore` o store `searchMap` dedicado) para evitar props en cadena. |
| **Dependencias** | T0. |
| **Entradas** | Clicks en tarjetas del listado; posible deselección al cambiar filtros (definir en T7). |
| **Salidas** | Store con `selectedRouteId`, acción `selectRoute` / `clearSelection`; componentes suscritos. |
| **Criterios de aceptación** | Al pulsar una ruta en el listado, el mapa reacciona (aunque la capa aún no pinte — eso es T3–T4). Al cambiar de ruta, la selección anterior se sustituye. |
| **Riesgos** | Doble fuente de verdad si se mantiene `selectedId` local en `RouteListPanel` sin migrar. |

---

## T2 — Decoder de geometría y path para Google Maps

| Campo | Valor |
|--------|--------|
| **Objetivo** | Utilidad pura (p. ej. `frontend/src/lib/polylineDecode.ts` o reutilizar lógica existente si hay) que, dado `RouteGeometryDetail` con `format === 'polyline'` (o equivalente del seed), devuelva `google.maps.LatLngLiteral[]`. Si `content` es **encoded polyline** (Google), usar `google.maps.geometry.encoding.decodePath` **después** de cargar la librería `geometry` en el loader, **o** implementación standalone del algoritmo encoded polyline. |
| **Dependencias** | T0; revisar formato exacto en `V2__bcn_routes_stops_seed.sql` / respuesta API (`format`, `content`). |
| **Entradas** | Array `geometries` del detalle de ruta. |
| **Salidas** | Función testeable + manejo de geometría ausente o formato desconocido → `null` o array vacío para activar fallback T3. |
| **Criterios de aceptación** | Con seed BCN, al menos una ruta produce path no vacío; sin geometría válida no lanza excepción. |
| **Riesgos** | Múltiples geometrías por snapshot — definir prioridad (p. ej. `type === 'full'`). |

---

## T3 — Fallback: polyline desde paradas ordenadas

| Campo | Valor |
|--------|--------|
| **Objetivo** | Si T2 no devuelve puntos suficientes, construir path ordenando `stops` por `sequence_order` y usando `latitude`/`longitude`; filtrar paradas sin coords. |
| **Dependencias** | T2. |
| **Entradas** | `RouteStopDetail[]` del mismo detalle que T2. |
| **Salidas** | `LatLngLiteral[]` para `Polyline`. |
| **Criterios de aceptación** | Ruta sin geometría útil pero con ≥2 paradas con lat/lon muestra línea que las une en orden. |
| **Riesgos** | Orden incorrecto si `sequence_order` no es denso (1,2,3…) — usar sort estable por `sequence_order`. |

---

## T4 — Composable / servicio de capa mapa: polyline + limpieza

| Campo | Valor |
|--------|--------|
| **Objetivo** | `useRouteMapOverlay` (o similar) que reciba instancia de `Map`, path, y cree/actualice **una** `google.maps.Polyline` (opciones de color/peso alineadas al tema). Al cambiar de ruta, **eliminar** polyline y marcadores anteriores (`setMap(null)`). |
| **Dependencias** | T1, T2, T3; `loadGoogleMaps` en `frontend/src/lib/googleMapsLoader.ts`. |
| **Entradas** | `selectedRouteId`, detalle en caché (`getRouteDetail` / `detailCache`). |
| **Salidas** | Sin fugas de listeners; `fitBounds` con padding sobre path + marcadores. |
| **Criterios de aceptación** | Cambiar entre dos rutas no deja polilíneas superpuestas; el viewport encuadra el recorrido. |
| **Riesgos** | Llamar antes de que el mapa esté listo — encolar o observar `mapReady`. |

---

## T5 — Marcadores numerados por parada

| Campo | Valor |
|--------|--------|
| **Objetivo** | Un marcador por parada con **número visible** = **`sequence_order`** (convención producto: coincide con orden operativo en BD; si en algún seed no empieza en 1, documentar o normalizar a 1…N en UI). |
| **Dependencias** | T4. |
| **Entradas** | Paradas con lat/lon del detalle. |
| **Salidas** | Implementación con **`Marker`** + `label` (texto corto) o icono SVG/`AdvancedMarkerElement` con pin personalizado; `title` accesible con nombre de parada. |
| **Criterios de aceptación** | Cada parada con coords muestra su número; sin coords se omite sin error. |
| **Riesgos** | Límite de caracteres en `label` de Marker clásico — usar símbolo compacto. |

---

## T6 — Integrar `GoogleMapContainer` con selección y detalle

| Campo | Valor |
|--------|--------|
| **Objetivo** | Conectar T1–T5: al cambiar `selectedRouteId`, cargar detalle si no está en caché (`loadDetail`), derivar path (T2→T3), pintar polyline y marcadores. |
| **Dependencias** | T1–T5; posible exposición de `map` instance desde `GoogleMapContainer` (ref + `defineExpose`) o mover creación del mapa al composable. |
| **Entradas** | Store + `useSearchRoutes` / API. |
| **Salidas** | Mapa funcional en `SearchMapPanel` sin romper geolocalización actual del contenedor. |
| **Criterios de aceptación** | Flujo manual: seleccionar ruta A → trazado A; seleccionar B → solo B; sin selección → comportamiento actual del mapa (centro default / usuario) o vacío explícito según decisión de producto. |
| **Riesgos** | Refactor grande del contenedor — mantener cambios mínimos y legibles. |

---

## T7 — Quitar «Recomendada» por defecto

| Campo | Valor |
|--------|--------|
| **Objetivo** | En `RouteListPanel` (o donde se arme `displayRoutes`), **eliminar** `recommended: i === 0`. El badge no aparece hasta que la lógica de T9 asigne `recommended` a **como máximo una** ruta en modo filtrado. |
| **Dependencias** | T0. |
| **Entradas** | Código actual de `displayRoutes`. |
| **Salidas** | Lista sin badge en carga inicial (salvo que T9 lo active en escenarios acordados). |
| **Criterios de aceptación** | Primera tarjeta ya no muestra «Recomendada» al cargar con datos reales. |
| **Riesgos** | Ninguno si se coordina con T9 en el mismo PR o inmediatamente después. |

---

## T8 — Botón «Filtrar» en la barra de filtros

| Campo | Valor |
|--------|--------|
| **Objetivo** | Añadir botón **Filtrar** al **final** de `SearchBookingBar.vue` (misma fila o bloque visual coherente con chips de fecha/hora), estilo alineado con botones existentes (emerald / neutral). |
| **Dependencias** | T0. |
| **Entradas** | Click del usuario. |
| **Salidas** | Emisión de evento o llamada a acción store: `applyRouteFilters()`; opcional estado `filtersDirty` si se quiere indicar cambios sin aplicar. |
| **Criterios de aceptación** | El botón es visible y accesible (teclado, `aria-label` i18n). **No** sustituye solo el `onMounted` de búsqueda: la primera carga sigue mostrando todas las rutas hasta que el usuario pulse Filtrar (o documentar híbrido: primera carga = todas; cambios posteriores requieren Filtrar — ver T9). |
| **Riesgos** | Duplicar lógica con `watch` en fecha — el prompt pide **gatillo explícito**; desactivar re-fetch automático al cambiar fecha **solo** para el listado filtrado si entra en conflicto con horarios (ver doc horarios). |

---

## T9 — Lógica de filtrado en cliente (fase 1) y resultados vacíos

| Campo | Valor |
|--------|--------|
| **Objetivo** | Mantener **`allResults`** (respuesta completa de `searchRoutes`) y **`displayedResults`** derivados. **Por defecto** `displayedResults === allResults`. Tras **Filtrar**, calcular subset según criterios mínimos documentados, p. ej.: **destino ida** = `outboundDestStopId` debe ser **parada de la ruta** (última parada o cualquier parada del detalle — **definir**); **origen** si hay coords de Places, distancia mínima a **primera parada** o a cualquier parada &lt; umbral; **hora** si hay `outboundTimeFilter` y datos de `daySchedules` / dep time (post–horarios). |
| **Dependencias** | T8; datos de `useSearchRoutes` + store; opcional precarga de detalles para todas las rutas al filtrar (coste N requests) o caché incremental. |
| **Entradas** | Estado del `useSearchStore`; resultados de búsqueda; detalle y horarios si existen. |
| **Salidas** | `displayedResults` vacío → mensaje i18n (sin crash); lista filtrada ordenada como hoy o por score. |
| **Criterios de aceptación** | Casos manuales: 0 / 1 / N rutas tras filtrar; sin selección de destino, definir si se ignoran filtros de parada o se muestra advertencia. |
| **Riesgos** | N+1 al filtrar — documentar límite o batch en fase 2 servidor. |

---

## T10 — Scoring «Recomendada» (como máximo una)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Función pura `pickRecommendedRoute(candidates, context)` → `routeId | null` donde `context` incluye coords origen, `outboundDestStopId`, filtros de hora y tiempos de salida si disponibles. **Regla ejemplo** (ajustable): entre candidatos filtrados, elegir la ruta con **menor** `departureTime` del día que cumpla «salida después de…»; empate → menor distancia haversine origen → primera parada con coords; si empate total → **ninguna** recomendada o la primera estable por `routeId` (documentar). |
| **Dependencias** | T7, T9. |
| **Entradas** | Lista filtrada no vacía. |
| **Salidas** | Una sola `recommended: true` en el modelo de vista o ninguna. |
| **Criterios de aceptación** | Con ≥2 rutas filtradas, como máximo un badge; con 1 ruta, opcional mostrar recomendada o no (decisión producto — documentar en README o ADR corto). |
| **Riesgos** | Producto puede querer recomendada solo si score supera umbral — dejar constante configurable. |

---

## T11 — i18n y microcopy

| Campo | Valor |
|--------|--------|
| **Objetivo** | Claves en `frontend/src/locales/es.json` para: botón **Filtrar**, lista vacía tras filtrar, opcional tooltip de filtros, textos de accesibilidad de marcadores. |
| **Dependencias** | T8, T9. |
| **Entradas** | Textos acordados con UX. |
| **Salidas** | Sin cadenas duras en español fuera de i18n para lo nuevo. |
| **Criterios de aceptación** | Cambiar idioma (si el proyecto añade más locales) no rompe claves. |
| **Riesgos** | Ninguno relevante. |

---

## T12 — Pruebas manuales y opcional unit tests

| Campo | Valor |
|--------|--------|
| **Objetivo** | Checklist en `frontend/README.md` o sección al pie de este doc: Docker + seed BCN; seleccionar rutas con/sin geometría; Filtrar con destino que reduce lista; Filtrar que deja 0; verificar una sola «Recomendada». Opcional: Vitest para decoder polyline (T2) y `pickRecommendedRoute` (T10). |
| **Dependencias** | T2–T11. |
| **Entradas** | Entorno local. |
| **Salidas** | Pasos reproducibles en &lt; 15 min. |
| **Criterios de aceptación** | Otro dev valida sin preguntar. |
| **Riesgos** | Ninguno. |

---

## T14 — Distancia a pie a la parada más cercana (tras Filtrar) + badge «Recomendada» &lt; 1 km

| Campo | Valor |
|--------|--------|
| **Objetivo** | Tras **Filtrar**, si el usuario eligió una dirección con **coordenadas** (Google Places), calcular la distancia **en línea recta** (aprox. a pie) desde ese punto hasta la **parada de la ruta más cercana**. En el **mapa**, al **seleccionar** una ruta filtrada, mostrar **marcador en la dirección**, **segmento** hasta la parada más cercana y **etiqueta** con la distancia. En la **tarjeta**, mostrar **«Recomendada»** si esa distancia es **&lt; 1 km** (pueden varias rutas); si hay coords pero **ninguna** ruta &lt; 1 km, conservar la recomendación por **horario** (T10). |
| **Dependencias** | T8–T10, T6; coords en `search` store desde `GooglePlacesInput`. |
| **Entradas** | `latitude`/`longitude` de `RouteStopDetail[]`; lat/lng del lugar Places (`geometry`). |
| **Salidas** | `walkDistanceByRouteId` en Pinia; `frontend/src/lib/walkToRoute.ts`, `walkOriginFromSearch.ts`; overlay en `GoogleMapContainer.vue`; lógica de badge en `RouteListPanel.vue`. |
| **Criterios de aceptación** | Sin Filtrar o sin coords de dirección, comportamiento anterior (sin tramo a pie en mapa; recomendada solo por horario). Con Filtrar + coords + ruta seleccionada: se ve origen + línea + texto de distancia aproximada. |
| **Riesgos** | Línea recta ≠ recorrido peatonal real; documentar en UI como aproximación. |

---

## T15 — Botón «Limpiar filtros» (volver al listado completo)

| Campo | Valor |
|--------|--------|
| **Objetivo** | Tras haber pulsado **Filtrar**, permitir **deshacer** el filtrado cliente **sin** volver a llamar al API: restaurar `results` desde `rawResults`, poner `filtersApplied` en falso, anular `recommendedRouteId` y `walkDistanceByRouteId`, y mantener `selectedRouteId` solo si la ruta sigue existiendo en el listado completo. |
| **Dependencias** | T8–T9 (`rawResults` + `applyRouteFilters`). |
| **Entradas** | Estado actual de `useRouteSearchStore`. |
| **Salidas** | Acción `clearAppliedFilters()` en Pinia; botón secundario junto a **Filtrar** en `SearchBookingBar` (tres layouts); clave i18n `search.filter.clear`. |
| **Criterios de aceptación** | El botón está deshabilitado hasta que haya filtros aplicados; al pulsarlo, el listado coincide con el de la carga inicial y no se muestran badges ni overlay a pie ligados al filtrado. |
| **Riesgos** | No borra los campos del formulario (solo el estado “filtrado”); documentar si en el futuro se desea “reset total”. |

---

## T13 — (Opcional / fase 2) Filtrado en servidor

| Campo | Valor |
|--------|--------|
| **Objetivo** | Si N+1 o reglas complejas lo exigen: ampliar **`POST /api/v1/search/routes`** (body ya con `origin`, `destination`, `date`, filtros de hora) para que **`SearchRoutesHandler`** filtre rutas cuyo snapshot contenga la parada destino y/o proximidad; devolver flag `recommendedRouteId` calculado en servidor. |
| **Dependencias** | T9 estable en producto. |
| **Entradas** | Contrato API y migraciones si hiciera falta índice espacial. |
| **Salidas** | Documentar en `docs/api_inventario_brecha.md`. |
| **Criterios de aceptación** | Misma UX con menos requests desde el cliente. |
| **Riesgos** | Acoplamiento y tiempo de respuesta — timeouts y caché. |

---

## Orden de ejecución sugerido

`T0 → … → T12 → T14 → T15`  
**T13** cuando haga falta escalar.

---

## Tabla resumen

| Id | Tema |
|----|------|
| T0 | Inventario brecha datos/UI |
| T1 | Estado compartido selección ruta (Pinia) |
| T2 | Decoder polyline / geometría API |
| T3 | Fallback path por paradas ordenadas |
| T4 | Composable polyline + limpieza + fitBounds |
| T5 | Marcadores con número (`sequence_order`) |
| T6 | Integración `GoogleMapContainer` + detalle |
| T7 | Quitar «Recomendada» fija en primera tarjeta |
| T8 | Botón Filtrar en `SearchBookingBar` |
| T9 | Filtrado cliente + resultados vacíos |
| T10 | Scoring: como máximo una recomendada |
| T11 | i18n |
| T12 | Verificación manual (+ tests opcionales) |
| T14 | Distancia a pie a parada cercana + badge &lt; 1 km |
| T15 | Limpiar filtros → listado completo (`rawResults`) |
| T13 | (Opcional) Filtrado y score en ms-router |

---

*Documento generado a partir de `docs/prompt_generador_tareas_mapa_filtros_buscador_rutas.md`.*

---

## Estado de implementación (abril 2026)

| Id | Estado | Notas |
|----|--------|--------|
| T0 | Hecho | Cubierto por este bloque + código. |
| T1 | Hecho | `useRouteSearchStore`: `selectedRouteId`, `setSelectedRouteId`. |
| T2 | Hecho | `frontend/src/lib/polylineDecode.ts` |
| T3 | Hecho | `frontend/src/lib/routePathFromDetail.ts` (`pathFromStops`) |
| T4–T6 | Hecho | `GoogleMapContainer.vue` — polyline, marcadores con `sequence_order`, `fitBounds`, limpieza al cambiar ruta. |
| T7 | Hecho | `RouteListPanel`: badge solo si `filtersApplied && recommendedRouteId`. |
| T8 | Hecho | `SearchBookingBar`: botón **Filtrar** (tres layouts de modo). |
| T9 | Hecho | `routeSearch.applyRouteFilters()` + `routeFilter.ts` (paradas, hora salida). |
| T10 | Hecho | `pickRecommendedRouteId` (≥2 candidatos, menor `departureTime`). |
| T11 | Hecho | `search.filter.apply`, `search.filter.clear`, `search.list.emptyAfterFilter`. |
| T12 | Pendiente | Checklist manual en README (opcional). |
| T14 | Hecho | `walkToRoute.ts`, `walkOriginFromSearch.ts`, `walkDistanceByRouteId`, `GooglePlacesInput` `coords-change`, overlay y badge en listado. |
| T15 | Hecho | `routeSearch.clearAppliedFilters()` + `SearchBookingBar`. |
| T13 | Pendiente | Filtrado en ms-router (fase 2). |
