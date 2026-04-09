import { importLibrary, setOptions } from '@googlemaps/js-api-loader'

let loadPromise: Promise<void> | null = null

export function getGoogleMapsApiKey(): string {
  return import.meta.env.VITE_GOOGLE_MAPS_API_KEY ?? ''
}

export function hasGoogleMapsApiKey(): boolean {
  return Boolean(getGoogleMapsApiKey().trim())
}

/**
 * Carga una sola vez `maps`, `places` y `routes` (Directions a pie sobre calles).
 * Tras resolver, `google.maps` queda disponible en el global.
 */
export function loadGoogleMaps(): Promise<void> {
  const key = getGoogleMapsApiKey()
  if (!key) {
    return Promise.reject(new Error('Falta VITE_GOOGLE_MAPS_API_KEY'))
  }
  if (!loadPromise) {
    setOptions({
      key,
      v: 'weekly',
      language: 'es',
    })
    loadPromise = Promise.all([
      importLibrary('maps'),
      importLibrary('places'),
      importLibrary('routes'),
    ]).then(() => undefined)
  }
  return loadPromise
}
