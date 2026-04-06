# Diseños y referencias visuales

Este directorio contiene **referencias visuales del producto** Route Searcher utilizadas para guiar el desarrollo del frontend. **No se usa en runtime**; es únicamente documentación de diseño.

## Propósito

- Centralizar mockups, wireframes y referencias de UI/UX.
- Servir como fuente de verdad visual para implementar vistas y componentes en el frontend.
- Evitar modificaciones automáticas: este directorio no debe ser alterado por herramientas de generación de código.

## Organización sugerida

| Carpeta | Contenido |
|--------|-----------|
| `mobile/` | Capturas y mockups de vistas móviles |
| `desktop/` | Capturas y mockups de vistas escritorio |
| `wireframes/` | Wireframes de baja/media fidelidad |
| `ux-flows/` | Diagramas o secuencias de flujos de usuario |
| `referencias-ui/` | Componentes UI, sistemas de diseño, exportaciones Figma |

Puedes añadir:
- Capturas de pantalla
- Mockups (PNG, PDF)
- Referencias a herramientas externas (ej. [Stitch](https://stitch.withgoogle.com/projects/13706256767192248996))

## Relación con el desarrollo del frontend

- El frontend (`frontend/`) debe implementar las pantallas y flujos reflejados en estos diseños.
- Consulta este directorio al implementar nuevas vistas o componentes para mantener coherencia visual y de flujo con el producto definido.

## Flujo del diseño

El flujo de la aplicación está compuesto por **cuatro pantallas principales** que representan el proceso completo de búsqueda y reserva de rutas.

### 1. Pantalla de búsqueda de rutas

Es la primera pantalla del flujo. Aquí el usuario puede buscar rutas disponibles.

**Funcionalidades principales:**

- Búsqueda de rutas
- Opción de buscar solo ida
- Opción de buscar ida y vuelta

**Versiones de diseño disponibles:**

- **Versión Desktop**  
  Referencias en: `pantalla-busqueda-version-desktop`

- **Versión Mobile** (dos variantes):
  - **Mobile con mapa** — Referencias en: `pantalla-busqueda-version-mapa-mobile`
  - **Mobile sin mapa** — Referencias en: `pantalla-busqueda-version-sin-mapa-mobile`

**Referencias de estilos para esta pantalla:**  
`referencias pantalla buscador`

---

### 2. Pantalla de review de booking

Después de seleccionar las rutas, el usuario pasa a la pantalla de review, donde puede revisar los datos de su reserva antes de continuar.

- **Diseños disponibles en:** `pantalla-confimacion-booking`
- **Referencias de estilos:** `referencias pantalla de review bookings`

---

### 3. Pantalla de pago

En esta pantalla el usuario selecciona el método de pago para completar la reserva.

- **Diseños disponibles en:** `pantalla-pago-booking`
- **Referencias de estilos:** `referencia pantalla pago`

---

### 4. Pantalla de confirmación de booking

Una vez completado el pago, el usuario llega a la pantalla de confirmación.

**Funcionalidades de esta pantalla:**

- Confirmación de la reserva
- Descarga del ticket en PDF
- Opción de añadir el ticket al wallet
- Visualización de la ruta del viaje en un mapa

- **Diseños disponibles en:** `pantalla-review-booking`
- **Referencias de estilos:** `referencias pantalla confirmacion`

---

### Objetivo de esta documentación

Este flujo sirve para:

- Guiar la implementación del frontend
- Identificar las pantallas del producto
- Localizar rápidamente las referencias visuales dentro del directorio `diseños/`
