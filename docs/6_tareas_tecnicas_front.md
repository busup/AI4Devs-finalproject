# 6. Tareas técnicas — frontend (pantalla de búsqueda)

Documento vivo alineado con `prompt_mejoras_vista_buscador.md`. **Última actualización:** implementación en el código del repositorio `frontend/`.

| Estado | ID | Tarea |
|--------|-----|--------|
| Hecho | T1 | Quitar indicador de pasos 1–2–3 (Ida / Vuelta / Revisión) |
| Hecho | T2 | Tres opciones de viaje: solo ida, solo vuelta, ida y vuelta |
| Hecho | T3 | Modo solo ida: origen Google, destino select paradas (mock) |
| Hecho | T4 | Modo solo vuelta: origen select mock, destino Google |
| Hecho | T5 | Modo ida y vuelta: cuatro campos con reglas ida/vuelta |
| Hecho | T6 | Fecha: calendario un solo día (`input type="date"` en popover) |
| Hecho | T7 | Hora: exclusivo salida o llegada + UI (`SearchTimeField`) |
| Hecho | T8 | Lista rutas con scroll; mapa en columna sticky con altura acotada |
| Hecho | T9 | Mapa Google Maps real (`GoogleMapContainer` + `VITE_GOOGLE_MAPS_API_KEY`) |

---

## Detalle por tarea

### T1 — Quitar pasos en cabecera

- **Implementación:** Eliminado `SearchProgressSteps.vue` y su uso en `SearchView.vue`.

### T2 — Tipo de viaje (tres opciones)

- **Implementación:** `TripMode` en `src/types/searchUi.ts` y `useSearchStore` (`outbound_only` | `return_only` | `roundtrip`). UI en `SearchBookingBar.vue`.

### T3–T5 — Campos según modo

- **Implementación:** `GooglePlacesInput.vue` (Places Autocomplete si hay clave), `StopSelect.vue` + `src/mocks/stops.ts`. Cuatro campos en secciones Ida/Vuelta para `roundtrip`.

### T6 — Fecha

- **Implementación:** `SearchDateField.vue` con `departureDate` en store (`YYYY-MM-DD`).

### T7 — Hora exclusiva

- **Implementación:** `SearchTimeField.vue` + `timeFilter` en store (`mode` + `hhmm`).

### T8 — Scroll / mapa fijo

- **Implementación:** Lista en `RouteListPanel` con `overflow-y-auto` y contenedor `max-h`; mapa en columna `lg:sticky` con `max-h`/`min-h` en `SearchView`.

### T9 — Google Maps

- **Implementación:** `@googlemaps/js-api-loader`, `src/lib/googleMapsLoader.ts`, `GoogleMapContainer.vue`. Variables `VITE_GOOGLE_MAPS_API_KEY` en `.env.example` y README.

---

## Referencias

- Prompt de mejoras: [prompt_mejoras_vista_buscador.md](./prompt_mejoras_vista_buscador.md)
- API: [4_especificacion_api.md](./4_especificacion_api.md)
