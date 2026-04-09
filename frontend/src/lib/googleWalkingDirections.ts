import { loadGoogleMaps } from '@/lib/googleMapsLoader'

export interface WalkingDirectionsResult {
  /** Puntos del recorrido peatonal por calles; vacío si Directions falla. */
  path: google.maps.LatLngLiteral[]
  distanceText: string | null
  durationText: string | null
  ok: boolean
}

function latLngLiteralFromLatLng(p: google.maps.LatLng): google.maps.LatLngLiteral {
  return { lat: p.lat(), lng: p.lng() }
}

function pathFromLeg(leg: google.maps.DirectionsLeg): google.maps.LatLngLiteral[] {
  const out: google.maps.LatLngLiteral[] = []
  for (const step of leg.steps ?? []) {
    if (!step.path) {
      continue
    }
    for (const p of step.path) {
      const q = latLngLiteralFromLatLng(p)
      const prev = out[out.length - 1]
      if (prev && prev.lat === q.lat && prev.lng === q.lng) {
        continue
      }
      out.push(q)
    }
  }
  return out
}

function pathFromOverview(route: google.maps.DirectionsRoute): google.maps.LatLngLiteral[] {
  const ov = route.overview_path
  if (!ov?.length) {
    return []
  }
  return ov.map(latLngLiteralFromLatLng)
}

/**
 * Ruta a pie entre dos puntos usando Directions Service (calles, no línea recta).
 * Requiere clave con **Directions API** habilitada en Google Cloud.
 */
export async function getWalkingDirectionsPath(
  origin: google.maps.LatLngLiteral,
  destination: google.maps.LatLngLiteral,
): Promise<WalkingDirectionsResult> {
  await loadGoogleMaps()

  const svc = new google.maps.DirectionsService()

  return new Promise((resolve) => {
    svc.route(
      {
        origin,
        destination,
        travelMode: google.maps.TravelMode.WALKING,
        unitSystem: google.maps.UnitSystem.METRIC,
        provideRouteAlternatives: false,
      },
      (result, status) => {
        if (status !== google.maps.DirectionsStatus.OK || !result?.routes?.length) {
          resolve({ path: [], distanceText: null, durationText: null, ok: false })
          return
        }
        const route = result.routes[0]
        const leg = route.legs?.[0]
        if (!leg) {
          resolve({ path: [], distanceText: null, durationText: null, ok: false })
          return
        }

        let path = pathFromLeg(leg)
        if (path.length < 2) {
          path = pathFromOverview(route)
        }

        resolve({
          path,
          distanceText: leg.distance?.text ?? null,
          durationText: leg.duration?.text ?? null,
          ok: path.length >= 2,
        })
      },
    )
  })
}
