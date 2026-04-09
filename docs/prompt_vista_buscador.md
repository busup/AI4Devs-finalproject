# Prompt: vista principal del buscador (única vista por ahora)

Documento listo para pegar en un agente de IA o entregar a un **desarrollador/a frontend** con Vue 3. Objetivo: implementar la **vista de búsqueda de rutas** como **pantalla principal y única** de la aplicación (hasta nuevas fases), con maquetación fiel al diseño de referencia y lista para conectar después con `src/services/routeSearcherApi.ts` (sin obligar a lógica real en esta tarea si no se indica).

---

## Rol

Actúa como **desarrollador/a frontend senior** (Vue 3, Composition API, TypeScript, Pinia, Vue Router, Tailwind CSS 3, Sass). Debes **añadir la vista del buscador** al proyecto existente en `frontend/`, extrayendo componentes reutilizables donde tenga sentido y manteniendo coherencia con el scaffold ya creado.

---

## Alcance

| Incluido | Fuera de alcance (salvo que se pida explícitamente) |
|----------|-----------------------------------------------------|
| Una vista de búsqueda como **ruta principal** (`/` o `/busqueda`) | Otras pantallas del flujo (review, pago, confirmación) |
| Cabecera, zona de progreso/pasos, barra de reserva (origen/destino, fecha, tipo de viaje), área de resultados o mapa según referencia | Integración real con backend (puede usarse mock o estado local) |
| Componentes en `frontend/src/components/` (p. ej. `layout/`, `search/`) | Modificar archivos bajo `diseños/` |

---

## Referencias de diseño (lectura obligatoria; no editar `diseños/`)

**Carpeta base (nombre exacto en disco, incluye un espacio al final del directorio):**

`diseños/referencias-ui/referencias pantalla buscador /`

### Aclaración importante

En esa referencia, el archivo **`app/review/page.tsx`** corresponde en el mock a la pantalla **“Review Your Booking”** (verificación final, tarjetas de ida/vuelta). El **formulario y flujo del buscador** (toggle Round-trip / One-way, origen, destino, pasos Outbound → Return → Review, barra de reserva, listado/mapa) está en **`app/page.tsx`** del mismo proyecto de referencia.

**Instrucción de implementación:**

1. **Contenido y layout del buscador:** replica la **estructura y bloques** de **`app/page.tsx`** (header con nav “Find Routes / My Bookings / Company Pass”, franja de progreso de pasos, barra de tipo de viaje, campos origen/destino, swap, fecha, botón de búsqueda, sección de resultados/mapa según el archivo).
2. **Sistema visual y patrones UI:** usa además **`app/review/page.tsx`** como referencia para **alinear** elementos que deben verse igual en todo el producto:
   - Cabecera: logo bus en recuadro `emerald-500`, marca **Route** (verde) + **Searcher** (neutral oscuro), acciones de usuario (tema, avatar) donde encaje con el buscador.
   - Fondos: `bg-neutral-50` / `bg-neutral-100`, bordes `border-neutral-200`, tarjetas `bg-white rounded-xl border`.
   - Tipografía: etiquetas en mayúsculas pequeñas (`text-[11px]`, `tracking-wider`, `text-neutral-500`), jerarquía de títulos y textos `text-neutral-800` / `text-neutral-500`.
   - Acentos: **emerald** (`text-emerald-500`, `bg-emerald-500`) para estados activos y paso actual.
   - Espaciado: contenedores `max-w-7xl` / `max-w-4xl`, padding `px-4 py-*`, breakpoints `sm:` / `lg:` como en la referencia.

No copies el texto “Review Your Booking” ni las tarjetas de journey de **`review/page.tsx`** como contenido principal del buscador; esos bloques sirven de **guía de estilo** y de **header** compartido, no como pantalla final del buscador.

**Otros archivos útiles de la misma referencia:** `app/layout.tsx`, `app/globals.css`, `styles/globals.css` (tokens y base), si necesitas contrastar variables o resets.

---

## Stack y ubicación en el repo

- **Framework:** Vue 3 + `<script setup>` + TypeScript.
- **Estilos:** Tailwind + Sass scoped donde ayude; **no** añadir shadcn/Radix de React: **traduce** patrones a HTML semántico + Tailwind (y componentes Vue propios).
- **Rutas:** Registrar la vista como **entrada principal** (sustituir o redirigir el placeholder actual que solo muestra texto de “estructura base”).
- **Estado:** Pinia opcional para origen/destino, tipo de viaje (ida / ida y vuelta), fecha; datos mock alineados con tipos en `src/types/api.ts` cuando se dispare la búsqueda.
- **API:** Llamadas a **`searchRoutes`** (u otras) solo si se pide en la misma tarea; si no, dejar handler preparado (`@click` → `console` o estado vacío) y comentario `// TODO: conectar routeSearcherApi`.

---

## i18n

- Textos visibles en **español** (o claves vue-i18n en `locales/es.json`) para títulos, etiquetas y botones, manteniendo el mismo significado que la referencia en inglés.

---

## Accesibilidad y responsive

- Etiquetas `<label>` asociadas, `aria-label` en iconos botón (swap, tema, etc.).
- Comportamiento responsive **como en la referencia** (columnas en móvil, filas en desktop, pasos visibles o adaptados).

---

## Entregables

1. Vista principal del buscador en `frontend/src/views/` (nombre coherente, p. ej. `SearchView.vue`).
2. Componentes extraídos bajo `frontend/src/components/search/` y/o `components/layout/` (header, barra de reserva, lista de resultados, etc.).
3. Router actualizado: ruta `/` (o `/busqueda`) renderiza esta vista; eliminar o sustituir el placeholder de “estructura base” en `App.vue` si ya no aplica.
4. Estilos coherentes con las referencias; **sin** modificar `diseños/`.
5. README del frontend o comentario breve en la PR: cómo abrir la vista y variables `VITE_*` si la búsqueda usa mock.

---

## Criterio de éxito

Un usuario abre la app y ve **solo** la experiencia de **búsqueda de rutas**, con aspecto visual alineado a **`referencias pantalla buscador /app/page.tsx`**, usando el **mismo lenguaje visual** que **`app/review/page.tsx`** (cabecera, colores, tarjetas, tipografía). El código queda preparado para enchufar `routeSearcherApi` sin rehacer la estructura de la vista.
