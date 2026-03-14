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
