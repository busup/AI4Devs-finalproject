# Prompt: generador de tareas técnicas — conexión front ↔ back (rutas y paradas desde BBDD)

**Uso:** copia el bloque siguiente (desde «Actúa como…» hasta el final) y pégalo en un asistente de IA. El resultado esperado es un **fichero Markdown nuevo** en `docs/` con tareas técnicas ordenadas y verificables.

---

## Bloque a ejecutar

Actúa como **desarrollador senior full-stack** (Vue 3 / TypeScript en front, APIs REST, MySQL, microservicios Laravel). Tu misión es **definir el conjunto de tareas técnicas** para **conectar el frontend con el backend** de forma que **el front consuma datos reales de rutas y de las paradas de esas rutas** almacenados en base de datos (ms-router y, si aplica, ms-planifications), en lugar de mocks o datos estáticos.

### Objetivo de producto

- El usuario debe poder **ver rutas** y **paradas asociadas** coherentes con el modelo persistido (`routes`, `route_snapshots`, `route_stops`, `stops`, `route_geometries` en **ms-router**).
- La integración debe respetar el **contrato API documentado** donde exista, y dejar explícitos los **huecos** (endpoints no implementados, tipos numéricos vs UUID, etc.).

### Contexto del repositorio (debes leerlo y citarlo en las tareas)

- Contrato y flujos: `docs/4_especificacion_api.md` (p. ej. `POST /api/v1/search/routes`, `GET /api/v1/routes/{routeId}/schedules`).
- Tipos TypeScript del front: `frontend/src/types/api.ts` (`SearchRoutesResponse`, `SearchRouteResultItem`, `AssignedStop`, etc.).
- Modelo de datos: `docs/3_Modelo_de_Datos.md`.
- API expuesta hoy por ms-router (Laravel): `ms-router/routes/api.php` y controladores bajo `ms-router/app/Http/Controllers/` (listar qué rutas HTTP existen realmente vs lo documentado en docs/4).
- Front: `frontend/` (Vite, Pinia si aplica, variables `VITE_*` en `.env.example`).
- Datos de ejemplo en BBDD: migración/seed BCN descrita en `docs/tareas_tecnicas_insercion_datos_rutas_bcn.md` si aplica.
- ADR de IDs: `docs/adr/0001-route-id-api-mapping.md` (UUID interno vs `routeId` numérico en contrato).

### Requisitos del entregable que debes producir ahora

1. **Crea un nuevo fichero Markdown** en **`docs/`** con nombre del estilo `docs/tareas_tecnicas_conexion_front_back_rutas_paradas.md` (o similar, descriptivo).

2. El documento debe listar **tareas técnicas** en orden lógico (inventario → contrato → backend → front → pruebas → despliegue local), cada una con:
   - **Objetivo** claro.
   - **Dependencias** entre tareas.
   - **Entradas / salidas** (ficheros, endpoints, tablas).
   - **Criterios de aceptación** verificables.
   - **Riesgos o decisiones** (CORS, autenticación, paginación, límites de resultados).

3. Las tareas deben cubrir **como mínimo**:
   - **Inventario de brecha:** qué endpoints del `docs/4` están implementados en qué servicio; qué falta para “listar / buscar rutas con paradas” desde el front.
   - **Contrato y tipos:** alinear `frontend/src/types/api.ts` con respuestas reales o definir capa de **mapeo** (UUID ↔ numérico, nombres de campos). Referenciar el ADR de IDs.
   - **Backend (ms-router o BFF):** exponer lectura de rutas y paradas por snapshot (o búsqueda geoespacial si el alcance lo exige): consultas a `routes` + `route_snapshots` + `route_stops` + `stops` (+ `route_geometries` para mapa). Incluir validación, códigos HTTP y errores en español si el proyecto lo exige.
   - **Backend (opcional ms-planifications):** si el contrato de búsqueda exige **horarios** (`schedules`, `trackId`), definir tareas para poblar o simular datos y exponer `GET .../schedules` coherente con `docs/4`.
   - **Frontend:** cliente HTTP (axios/fetch), base URL desde env, composables o store para estado de búsqueda, componentes que **rendericen rutas y lista u orden de paradas** desde la API (no hardcode). Manejo de carga y error.
   - **Mapa / geometría:** si la UI muestra polilínea, tarea para consumir `route_geometries` o campo equivalente y decodificar polyline en cliente si aplica.
   - **CORS y red Docker:** documentar origen del front (`localhost` distinto al del API) y variables necesarias en compose o proxy Vite.
   - **Pruebas:** manual (flujo en navegador) y, si el repo las usa, tests de contrato o e2e mínimos.

4. **Tono:** técnico, accionable, sin código largo salvo pseudocódigo; el foco es la **lista de tareas** y decisiones.

5. Incluir al final un **orden de ejecución sugerido** y una tabla **resumen** (T1, T2, …).

---

## Notas para quien mantenga este fichero

- Si cambia `docs/4_especificacion_api.md` o los controladores Laravel, actualiza el bloque «Contexto del repositorio».
- Tras ejecutar el prompt, revisa el `.md` generado y ajusta nombres de endpoints a la realidad del código.
