/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_API_BASE_URL: string
  /** ms-planifications (horarios por snapshot). Ej. http://localhost:8002/api/v1 o /dev-ms-planifications/api/v1 */
  readonly VITE_API_PLANIFICATIONS_BASE_URL?: string
  readonly VITE_USE_MOCK_API?: string
  /** Clave de la API JavaScript de Google Maps (Maps + Places). */
  readonly VITE_GOOGLE_MAPS_API_KEY?: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
