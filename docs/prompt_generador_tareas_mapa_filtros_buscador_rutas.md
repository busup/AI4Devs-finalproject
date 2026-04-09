# Prompt: generador de tareas técnicas — mapa con ruta, filtros explícitos y badge «Recomendada»

**Uso:** copia el bloque siguiente (desde «Actúa como…» hasta el final) y pégalo en un asistente de IA. El resultado esperado es un **fichero Markdown nuevo** en `docs/` con tareas técnicas ordenadas y verificables (p. ej. `docs/tareas_tecnicas_mapa_filtros_buscador_rutas.md`).

---

## Bloque a ejecutar

Actúa como **desarrollador senior full-stack** (Vue 3 / TypeScript, Google Maps JavaScript API, APIs REST, Laravel, MySQL). Tu misión es **definir el conjunto de tareas técnicas** para el **buscador de rutas** del repositorio, cubriendo **mapa al seleccionar ruta**, **botón de filtrar**, **eliminación del uso por defecto de «Recomendada»** y **filtrado de resultados con una única ruta recomendada** cuando aplique.

### Contexto del repositorio (referencia)

- Vista principal: `frontend/src/views/SearchView.vue` — listado `RouteListPanel`, mapa `SearchMapPanel` → `GoogleMapContainer.vue`.
- Listado: `frontend/src/components/search/RouteListPanel.vue` — hoy marca la **primera** ruta como `recommended: true` de forma fija; existe un botón UI «Mañana» sin lógica de filtrado conectada.
- Barra de filtros: `frontend/src/components/search/SearchBookingBar.vue` — modo de viaje, origen/destino (Google + selects de paradas), fecha, horarios (`useSearchStore`: `departureDate`, `outboundTimeFilter`, `returnTimeFilter`, `tripMode`, etc.).
- Datos de rutas: `useSearchRoutes` + `POST /api/v1/search/routes` (ms-router); detalle por ruta `GET /api/v1/routes/{id}/schedules` devuelve **`stops`** (con `latitude`, `longitude`, `sequence_order`, `alias`, `name`) y **`geometries`** (`type`, `format`, `content` — p. ej. polyline codificada en seed BCN).
- Paradas terminales para selects: `GET /api/v1/stops/route-terminals` (última parada por ruta).
- El **body** de búsqueda ya puede incluir origen/destino y fecha; el handler **`SearchRoutesHandler`** (ms-router) hoy devuelve **todas** las rutas aprobadas **sin** filtrado geoespacial ni por hora — documentar si el filtrado será **solo front**, **ampliación de POST search** o ambos.

### Requisitos funcionales a desglosar en tareas

#### 1 — Mapa al seleccionar una ruta

- Al **seleccionar** una ruta en el listado, en el mapa se debe:
  - **Dibujar la polyline** del recorrido: preferencia por **geometría publicada** del snapshot (`geometries` con formato polyline / encoded path); si falta o no es usable, **fallback**: polilínea que una las paradas en orden `sequence_order` usando `latitude`/`longitude` del detalle.
  - **Dibujar cada parada** con un **marcador** que muestre en el centro un **número** = orden de parada (`sequence_order` o 1…N según producto; documentar convención).
- Considerar: límites de la API de Google (Markers vs AdvancedMarkerElement, `Map`/`Polyline`, rendimiento al cambiar de ruta, limpieza de overlays previos, `fitBounds` al trazado, accesibilidad del marcador).

#### 2 — Botón «Filtrar»

- Añadir un **botón de filtrar** al **final** de la zona de filtros (barra de búsqueda), coherente con el diseño actual.
- El botón es el **gatillo explícito** para aplicar filtros a la búsqueda de rutas (no solo el cambio reactivo de campos, salvo que el documento de tareas decida un híbrido y lo justifique).

#### 3 — Quitar «Recomendada» por defecto

- **Eliminar** el comportamiento actual que muestra la etiqueta **«Recomendada»** en la **primera** tarjeta **siempre**.
- Reservar el badge **solo** cuando exista una **mejor opción** justificada por la lógica de filtrado / scoring (ver punto 4).

#### 4 — Resultados: todos por defecto; filtrados al pulsar «Filtrar»; una recomendada

- **Por defecto** (carga inicial o sin filtrado aplicado): mostrar **todas** las rutas devueltas por la búsqueda base (comportamiento actual de listado completo).
- Tras pulsar **«Filtrar»** (usando los datos que el usuario haya puesto en filtros: origen, destino parada, fecha, restricciones de hora de ida/vuelta según modo, etc.): mostrar **solo** las rutas que **cumplan** los criterios acordados.
- Entre las rutas **filtradas**, **como máximo una** (o cero si ninguna destaca) debe llevar el mensaje **«Recomendada»**, según una **regla explícita** (p. ej. menor distancia tiempo a primera parada relevante, mejor ajuste a ventana horaria, heurística documentada).
- Si **ninguna** ruta pasa el filtro: estado vacío claro (i18n), sin crash.
- Documentar si el filtrado es **cliente** (sobre resultados + detalle ya cargado o bajo demanda) o **servidor** (nuevo contrato o query params en `POST /search/routes`) y trade-offs.

### Requisitos del entregable que debes producir ahora

1. **Crea un nuevo fichero Markdown** en **`docs/`** con nombre del estilo **`docs/tareas_tecnicas_mapa_filtros_buscador_rutas.md`**.

2. El documento debe listar **tareas técnicas** en orden lógico, cada una con: **objetivo**, **dependencias**, **entradas/salidas**, **criterios de aceptación** verificables, **riesgos/decisiones**.

3. Las tareas deben cubrir **como mínimo**:
   - **Estado compartido** listado ↔ mapa (Pinia o props/events): `selectedRouteId`, geometría/paradas cargadas o en caché.
   - **Capa mapa**: servicio o composable (`useRouteMapOverlay`, etc.) para polyline + marcadores numerados; decodificación polyline si aplica (`frontend/src/types/api.ts` — `RouteGeometryDetail`).
   - **UI filtros**: ubicación y estilos del botón **Filtrar**; posible estado `filtersApplied` / `lastFilterSnapshot`.
   - **Lógica de filtrado**: criterios concretos alineados con datos disponibles (coords paradas, horarios ms-planifications si ya están en front, etc.).
   - **Scoring «Recomendada»**: función pura o módulo testeable; casos límite (empate, una sola ruta, cero rutas).
   - **i18n** (`frontend/src/locales/es.json`) para nuevos textos (botón, vacíos, tooltip si aplica).
   - **Pruebas manuales**: flujo con varias rutas seed BCN; con y sin geometría; con filtro que deja 0/1/N resultados.
   - **Opcional:** tests unitarios del filtro/score o del decoder de polyline.

4. **Tono:** técnico, accionable; poco código salvo pseudocódigo o referencias a archivos/rutas.

5. Incluir al final **orden de ejecución sugerido** y tabla **resumen (T1, T2, …)**.

6. Si detectas **dependencias con documentos existentes**, enlázalos (`docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md`, `docs/tareas_tecnicas_horarios_front_rutas_itinerario.md`, `docs/api_inventario_brecha.md`).

---

## Notas para quien ejecute las tareas después

- Reutilizar **`VITE_GOOGLE_MAPS_API_KEY`** y `loadGoogleMaps` (`frontend/src/lib/googleMapsLoader.ts`).
- No inventar datos de recorrido: priorizar **`geometries`** del API; fallback solo cuando falte geometría válida.
- Mantener el alcance acotado: no rediseñar toda la vista salvo lo necesario para sincronizar mapa y listado.

---

*Documento generado para alinear el trabajo de mapa, filtrado explícito y badge «Recomendada» con criterio.*
