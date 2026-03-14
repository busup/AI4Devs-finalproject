# Route Searcher

Arquitectura basada en **microservicios** para búsqueda y reserva de rutas corporativas (Home → Work → Home). Este monorepo contiene el frontend, el API Gateway y todos los microservicios definidos en la documentación técnica.

## Requisitos

- **Docker** y **Docker Compose**
- **Node.js** (para desarrollo local del frontend sin Docker)
- **PHP 8.1** (para desarrollo local del Routes service)
- **Go 1.21+** (para desarrollo local de Rates y Search, si aplica)

## Instalación

```bash
git clone <repository-url>
cd AI4Devs-finalproject
docker compose up
```

- **API Gateway:** http://localhost:8080  
- **Frontend (dev):** http://localhost:5173  

Para levantar solo los servicios de backend y el gateway (sin frontend en Docker), puedes comentar o eliminar el servicio `frontend` en `docker-compose.yml` y ejecutar el frontend en local con `cd frontend && npm install && npm run dev`.

## Servicios

| Servicio        | Puerto (interno) | Responsabilidad                                      |
|-----------------|-------------------|------------------------------------------------------|
| **gateway**     | 8080 (expuesto)   | Punto de entrada único; enruta `/api/v1/*` a cada microservicio |
| **frontend**    | 5173 (expuesto)   | Interfaz de usuario (Vue 3, Vite, Pinia, Tailwind)   |
| **routes-service** | 8000           | Líneas, paradas, horarios (Laravel 10 / PHP 8.1)     |
| **sites-service**  | 3001           | Sitios corporativos, configuración, usuarios (NestJS) |
| **booking-service** | 3002         | Reservas, disponibilidad (NestJS)                   |
| **rates-service**   | 3005         | Reglas de tarifas, precios (Go)                      |
| **search-service**  | 3004         | Búsqueda geoespacial, read-model (Go + PostGIS)       |

Cada microservicio tiene su propia base de datos (Database-per-Service). Las rutas del API están definidas en `docs/4_especificacion_api.md`.

## Estructura del monorepo

```
frontend/           # Vue 3 + Vite + Pinia + Tailwind
services/
  routes-service/   # Laravel 10
  sites-service/    # NestJS
  booking-service/  # NestJS
  rates-service/    # Go
  search-service/   # Go + PostGIS
gateway/            # Nginx (proxy hacia los servicios)
infra/              # Configuración de infraestructura
docs/               # Documentación técnica (no modificar automáticamente)
diseños/            # Referencias visuales del producto
```

## Documentación

- `docs/1_descripcion_general.md` — Producto e instalación
- `docs/2_Arquitectura_del_Sistema.md` — Microservicios y flujos
- `docs/3_Modelo_de_Datos.md` — Esquemas por servicio
- `docs/4_especificacion_api.md` — Contratos API

Este scaffolding prioriza **estructura**, **configuración** y **ejecutabilidad local**. La lógica de negocio debe implementarse según la documentación anterior.
