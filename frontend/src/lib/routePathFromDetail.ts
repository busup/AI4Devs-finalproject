import { decodeEncodedPolyline, type LatLngLiteral } from '@/lib/polylineDecode'
import type { RouteDetailResponse, RouteGeometryDetail, RouteStopDetail } from '@/types'

function pickPolylineGeometry(geometries: RouteGeometryDetail[] | undefined): RouteGeometryDetail | null {
  if (!geometries?.length) {
    return null
  }
  const full = geometries.find((g) => g.type?.toLowerCase() === 'full' && g.content?.length)
  if (full) {
    return full
  }
  const anyPoly = geometries.find(
    (g) => g.format?.toLowerCase() === 'polyline' && g.content?.length,
  )
  return anyPoly ?? null
}

export function pathFromStops(stops: RouteStopDetail[]): LatLngLiteral[] {
  const sorted = [...stops].sort((a, b) => a.sequence_order - b.sequence_order)
  const out: LatLngLiteral[] = []
  for (const s of sorted) {
    const lat = s.latitude
    const lng = s.longitude
    if (typeof lat === 'number' && typeof lng === 'number' && Number.isFinite(lat) && Number.isFinite(lng)) {
      out.push({ lat, lng })
    }
  }
  return out
}

/**
 * Prioridad: geometría encoded polyline del snapshot; si no, línea que une paradas por `sequence_order`.
 */
export function buildRoutePathFromDetail(detail: RouteDetailResponse): LatLngLiteral[] {
  const geo = pickPolylineGeometry(detail.geometries)
  if (geo?.content) {
    try {
      const decoded = decodeEncodedPolyline(geo.content.trim())
      if (decoded.length >= 2) {
        return decoded
      }
    } catch {
      /* continuar con fallback */
    }
  }
  return pathFromStops(detail.stops ?? [])
}
