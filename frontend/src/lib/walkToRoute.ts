import type { RouteStopDetail } from '@/types'

const EARTH_RADIUS_M = 6_371_000

export interface LatLng {
  lat: number
  lng: number
}

/** Distancia en metros (aprox. geodésica). */
export function haversineDistanceMeters(a: LatLng, b: LatLng): number {
  const φ1 = (a.lat * Math.PI) / 180
  const φ2 = (b.lat * Math.PI) / 180
  const Δφ = ((b.lat - a.lat) * Math.PI) / 180
  const Δλ = ((b.lng - a.lng) * Math.PI) / 180

  const s =
    Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2)
  const c = 2 * Math.atan2(Math.sqrt(s), Math.sqrt(1 - s))

  return EARTH_RADIUS_M * c
}

export interface NearestStopWalkResult {
  stop: RouteStopDetail
  distanceM: number
  position: LatLng
}

/** Parada con coordenadas más cercana al punto de origen (línea recta ≈ distancia a pie aproximada). */
export function nearestStopWalkFromOrigin(
  origin: LatLng,
  stops: RouteStopDetail[],
): NearestStopWalkResult | null {
  let best: NearestStopWalkResult | null = null

  for (const s of stops) {
    const lat = s.latitude
    const lng = s.longitude
    if (typeof lat !== 'number' || typeof lng !== 'number') {
      continue
    }
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
      continue
    }
    const pos: LatLng = { lat, lng }
    const d = haversineDistanceMeters(origin, pos)
    if (!best || d < best.distanceM) {
      best = { stop: s, distanceM: d, position: pos }
    }
  }

  return best
}

/** Distancia mínima en metros al conjunto de paradas, o `null` si no hay coords. */
export function minWalkDistanceToRouteMeters(origin: LatLng, stops: RouteStopDetail[]): number | null {
  const n = nearestStopWalkFromOrigin(origin, stops)
  return n ? n.distanceM : null
}

export function formatWalkDistanceMeters(m: number): string {
  if (m < 1000) {
    return `${Math.round(m)} m`
  }
  return `${(m / 1000).toFixed(1)} km`
}
