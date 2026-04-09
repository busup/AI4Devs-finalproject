import type { TripMode } from '@/types/searchUi'

/** Subconjunto del store de búsqueda para calcular el punto “a pie hasta la ruta”. */
export interface SearchCoordsState {
  tripMode: TripMode
  outboundOriginLat: number | null
  outboundOriginLng: number | null
  returnDestLat: number | null
  returnDestLng: number | null
}

/**
 * Punto de partida para “caminar hasta la ruta”: ida = origen Places; solo vuelta = destino Places del tramo vuelta.
 * En ida y vuelta se usa el origen de ida (primer tramo).
 */
export function getWalkOriginLatLng(store: SearchCoordsState): { lat: number; lng: number } | null {
  const mode = store.tripMode
  if (mode === 'return_only') {
    const lat = store.returnDestLat
    const lng = store.returnDestLng
    if (lat != null && lng != null && Number.isFinite(lat) && Number.isFinite(lng)) {
      return { lat, lng }
    }
    return null
  }
  const lat = store.outboundOriginLat
  const lng = store.outboundOriginLng
  if (lat != null && lng != null && Number.isFinite(lat) && Number.isFinite(lng)) {
    return { lat, lng }
  }
  return null
}

export function hasWalkOriginCoords(store: SearchCoordsState): boolean {
  return getWalkOriginLatLng(store) !== null
}
