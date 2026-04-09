# Prompt: mejoras técnicas de la pantalla de búsqueda

Documento para un **desarrollador/a frontend senior** o agente de IA. Objetivo: **evolucionar** la vista de búsqueda existente en `frontend/` (Vue 3, Pinia, Tailwind, etc.) según las tareas siguientes. **No** modificar archivos bajo `diseños/` salvo lectura de referencia.

---

## Documento obligatorio de tareas (`docs/6_tareas_tecnicas_front.md`)

Al **planificar o ejecutar** estas mejoras, las tareas técnicas deben quedar **registradas y mantenidas** en el fichero:

**`docs/6_tareas_tecnicas_front.md`**

**Reglas:**

1. **Crear** el archivo si no existe; **actualizarlo** en cada iteración (estado de tareas, notas, enlaces a PRs o commits si se desea).
2. El documento debe incluir como mínimo: **identificador** por tarea (p. ej. T1–T9), **descripción breve**, **criterios de hecho** y **estado** (pendiente / en curso / hecho).
3. Las secciones detalladas de este prompt (puntos 1–9 más abajo) son la **fuente funcional**; `6_tareas_tecnicas_front.md` es el **registro operativo** para seguimiento y revisión de código.
4. No sustituye a commits ni PRs, pero debe reflejar el alcance acordado antes de dar por cerrada la epic de mejoras.

Si un agente de IA ejecuta este prompt, su **primer entregable** debe ser **actualizar `docs/6_tareas_tecnicas_front.md`** (tabla de estado + detalle), y en paralelo o después la implementación en `frontend/`.

---

## Contexto

- Vista actual: `frontend/src/views/SearchView.vue` y componentes en `frontend/src/components/search/` y `frontend/src/components/layout/`.
- Estado: `frontend/src/stores/search.ts`, servicios: `frontend/src/services/routeSearcherApi.ts`, tipos en `frontend/src/types/`.
- i18n: `frontend/src/locales/es.json`.

---

## Tareas técnicas (orden sugerido de implementación)

### 1. Eliminar el indicador de pasos 1–2–3 (Ida / Vuelta / Revisión)

- **Qué:** Quitar de la UI el bloque que muestra los tres pasos numerados en la cabecera (componente tipo `SearchProgressSteps` o equivalente).
- **Por qué:** La pantalla de búsqueda será **una vista única**; no se guía al usuario por un wizard de pasos en esta pantalla.
- **Entregable:** El layout pasa directamente de cabecera (`AppHeader`) + barra de búsqueda al contenido principal (lista + mapa), sin franja de progreso de pasos.

---

### 2. Redefinir las opciones de tipo de viaje

- **Qué:** Sustituir el toggle actual (p. ej. “Ida y vuelta” / “Solo ida”) por **tres** opciones explícitas:
  - **Solo ida**
  - **Solo vuelta**
  - **Ida y vuelta**
- **Estado:** Ampliar el modelo en Pinia (`TripType` o nombre equivalente) y los valores posibles para reflejar estos tres casos.
- **i18n:** Claves en español coherentes en `locales/es.json`.

---

### 3. Comportamiento si el usuario elige **Solo ida**

- **Origen:** Campo de **búsqueda de direcciones** integrado con **Google** (Places Autocomplete, Geocoding o el flujo que defina el equipo; documentar API y variable de entorno, p. ej. `VITE_GOOGLE_MAPS_API_KEY`).
- **Destino:** **Desplegable (select)** con **paradas predefinidas**; los datos son **mock** en frontend hasta existir API (lista estática o store con array mockeado documentado en código o `src/mocks/`).

---

### 4. Comportamiento si el usuario elige **Solo vuelta**

- **Origen:** **Desplegable** con paradas predefinidas (**mock**, misma fuente o convención que en el punto 3).
- **Destino:** **Búsqueda de direcciones con Google** (misma integración que el origen en “Solo ida”).

---

### 5. Comportamiento si el usuario elige **Ida y vuelta**

- Mostrar **cuatro** campos de localización en el orden lógico del viaje:
  - Ida: **origen** y **destino**
  - Vuelta: **origen** y **destino**
- **Reglas por tramo (reutilizar la lógica de los puntos 3 y 4):**
  - En el tramo de **ida:** origen = búsqueda Google; destino = desplegable de paradas (mock).
  - En el tramo de **vuelta:** origen = desplegable de paradas (mock); destino = búsqueda Google.
- **UI:** Mantener una maquetación clara (secciones “Ida” / “Vuelta” o etiquetas equivalentes) sin duplicar innecesariamente componentes: extraer componentes reutilizables (p. ej. `LocationFieldGoogle.vue`, `StopSelect.vue`).

---

### 6. Fecha de salida: calendario para un solo día

- Al **hacer clic** en el campo de fecha de salida, abrir un **componente de calendario** que permita seleccionar **un único día** (no rango).
- Puede usarse un datepicker accesible compatible con Vue 3 o un calendario custom; debe funcionar bien en móvil y desktop.
- Persistir la fecha en el store en formato acordado con la API futura (p. ej. `YYYY-MM-DD`).

---

### 7. Campo de hora: renombrar y selector salida **o** llegada

- **Etiqueta:** Sustituir “Pref. hora de llegada” por algo neutro tipo **“Hora”** (o “Horario”, según copy final en i18n).
- **Comportamiento:** Al hacer clic, mostrar un componente (modal, popover o panel) que permita al usuario indicar **una sola** restricción de tiempo:
  - **Salida después de…** **o**
  - **Llegada antes de…**
- El usuario **no** puede activar ambas a la vez: es **exclusivo** (radio, pestañas o selector único). Reflejar en el modelo (p. ej. `timeFilter: { mode: 'departure' | 'arrival', value: 'HH:mm' } | null`).
- Alinear tipos con `docs/4_especificacion_api.md` (`departureAfter` / `arrivalBefore`) cuando se conecte la API.

---

### 8. Scroll en lista de rutas y mapa fijo

- Si hay **muchas** opciones de rutas, la **lista** de resultados debe ser **scrollable** dentro de su columna o contenedor.
- El **panel del mapa** debe mantener **altura fija** (o sticky) de modo que, al hacer scroll en la lista, el mapa **no** se desplace verticalmente con el listado; el usuario siempre ve el mapa en la misma zona de pantalla (comportamiento tipo “lista scroll, mapa fijo”).
- Revisar breakpoints: en móvil puede requerirse orden distinto (lista arriba / mapa abajo) pero manteniendo la idea de área de mapa con altura definida y lista con scroll independiente donde aplique.

---

### 9. Mapa: Google Maps

- Sustituir el **placeholder** actual del mapa por un mapa real de **Google Maps** (JavaScript API / `@googlemaps/js-api-loader` o equivalente oficial).
- Requisitos mínimos:
  - Carga diferida de la API con clave en variable de entorno (`VITE_GOOGLE_MAPS_API_KEY` o nombre acordado).
  - Documentar en `frontend/README.md` la creación de clave, restricciones (HTTP referrer) y variables necesarias.
  - Mostrar al menos marcadores o ruta de ejemplo si aún no hay datos reales del backend; preparar el mapa para recibir coordenadas desde el store o desde la respuesta de `searchRoutes` en una fase posterior.

---

## Restricciones generales

- No eliminar la integración existente con `routeSearcherApi` / tipos; **extender** o adaptar cuando se conecten búsqueda real y filtros de hora/fecha.
- Mantener **accesibilidad** (teclado, `aria-*`, foco en modales/datepickers).
- **No** modificar el contenido de `diseños/`; solo lectura si hace falta coherencia visual.

---

## Entregables esperados

1. **`docs/6_tareas_tecnicas_front.md`** creado o actualizado con todas las tareas (T1–T9), estados y criterios de hecho alineados con este documento.
2. PR o commits con los cambios anteriores documentados brevemente.
3. `.env.example` actualizado con variables Google (Maps + Places si aplica).
4. `frontend/README.md` actualizado: nuevas env vars, cómo ejecutar con mapa y autocompletado.
5. Store y tipos TypeScript coherentes con los tres modos de viaje y con el filtro de hora exclusivo.

---

## Criterio de éxito

- **`docs/6_tareas_tecnicas_front.md`** actualizado y coherente con lo implementado.
- Vista única **sin** pasos 1–2–3 en cabecera.
- Tres modos de viaje con reglas de campos (Google vs desplegable mock) correctamente aplicadas, incluyendo **ida y vuelta** con cuatro campos.
- Fecha con calendario de **un día**; hora con selector **salida o llegada** (exclusivo).
- Lista de rutas con **scroll** y mapa **fijo** en su zona.
- Mapa renderizado con **Google Maps** y documentación de configuración local.
