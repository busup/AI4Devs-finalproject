<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import { buildRoutePathFromDetail } from '@/lib/routePathFromDetail'
import { getWalkingDirectionsPath } from '@/lib/googleWalkingDirections'
import { hasGoogleMapsApiKey, loadGoogleMaps } from '@/lib/googleMapsLoader'
import { getWalkOriginLatLng } from '@/lib/walkOriginFromSearch'
import { formatWalkDistanceMeters, nearestStopWalkFromOrigin } from '@/lib/walkToRoute'
import { useRouteSearchStore } from '@/stores/routeSearch'
import { useSearchStore } from '@/stores/search'

const mapEl = ref<HTMLDivElement | null>(null)
const error = ref<string | null>(null)

/** Área del mapa fija 800×700 px (sin max-w-full para no reducir el ancho). */
const MAP_BOX = 'h-[700px] w-[800px] shrink-0 min-w-0'

const DEFAULT_CENTER = { lat: 40.4168, lng: -3.7038 }
const DEFAULT_ZOOM = 11

const { t } = useI18n()
const routeSearchStore = useRouteSearchStore()
const searchStore = useSearchStore()

let mapInstance: google.maps.Map | null = null
let userMarker: google.maps.Marker | null = null
let routePolyline: google.maps.Polyline | null = null
const routeMarkers: google.maps.Marker[] = []
let walkPolyline: google.maps.Polyline | null = null
let walkOriginMarker: google.maps.Marker | null = null
let walkInfoWindow: google.maps.InfoWindow | null = null

function clearRouteOverlays() {
  routePolyline?.setMap(null)
  routePolyline = null
  for (const m of routeMarkers) {
    m.setMap(null)
  }
  routeMarkers.length = 0
  walkPolyline?.setMap(null)
  walkPolyline = null
  walkOriginMarker?.setMap(null)
  walkOriginMarker = null
  walkInfoWindow?.close()
  walkInfoWindow = null
}

function fitMapToProvinceOrUser(
  map: google.maps.Map,
  marker: google.maps.Marker,
  userLoc: google.maps.LatLngLiteral,
) {
  const geocoder = new google.maps.Geocoder()
  geocoder.geocode({ location: userLoc }, (results, status) => {
    if (status !== 'OK' || !results?.length) {
      map.setCenter(userLoc)
      map.setZoom(9)
      marker.setPosition(userLoc)
      marker.setTitle('Tu ubicación')
      return
    }

    const applyBounds = (r: google.maps.GeocoderResult) => {
      if (r.geometry?.viewport) {
        map.fitBounds(r.geometry.viewport)
      } else if (r.geometry?.bounds) {
        map.fitBounds(r.geometry.bounds)
      } else {
        map.setCenter(userLoc)
        map.setZoom(9)
      }
      marker.setPosition(userLoc)
      marker.setTitle('Tu ubicación')
    }

    const byType = (t: string) => results.find((r) => r.types.includes(t))
    const provinceByType = byType('administrative_area_level_2')
    if (provinceByType) {
      applyBounds(provinceByType)
      return
    }

    for (const r of results) {
      const hasProvince = r.address_components?.some((c) =>
        c.types.includes('administrative_area_level_2'),
      )
      if (hasProvince && (r.geometry?.viewport || r.geometry?.bounds)) {
        applyBounds(r)
        return
      }
    }

    const regionByType = byType('administrative_area_level_1')
    if (regionByType) {
      applyBounds(regionByType)
      return
    }

    for (const r of results) {
      const hasRegion = r.address_components?.some((c) =>
        c.types.includes('administrative_area_level_1'),
      )
      if (hasRegion && (r.geometry?.viewport || r.geometry?.bounds)) {
        applyBounds(r)
        return
      }
    }

    map.setCenter(userLoc)
    map.setZoom(9)
    marker.setPosition(userLoc)
    marker.setTitle('Tu ubicación')
  })
}

/** Trazado tipo “búsqueda en Maps”: puntos a lo largo del recorrido, sin línea continua. */
function createDottedWalkPolyline(
  map: google.maps.Map,
  path: google.maps.LatLngLiteral[],
): google.maps.Polyline {
  return new google.maps.Polyline({
    path,
    geodesic: false,
    strokeOpacity: 0,
    icons: [
      {
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          fillColor: '#2563eb',
          fillOpacity: 1,
          strokeColor: '#ffffff',
          strokeWeight: 1,
          scale: 4,
        },
        offset: '0',
        repeat: '12px',
      },
    ],
    zIndex: 2,
    map,
  })
}

function tryLocateUser(map: google.maps.Map, marker: google.maps.Marker) {
  if (!navigator.geolocation) return

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const userLoc = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude,
      }
      fitMapToProvinceOrUser(map, marker, userLoc)
    },
    () => {
      /* Permiso denegado o error: se mantiene el centro por defecto */
    },
    { enableHighAccuracy: false, timeout: 12000, maximumAge: 600000 },
  )
}

async function paintSelectedRoute(routeId: string | null) {
  clearRouteOverlays()
  if (!routeId || !mapInstance) {
    return
  }
  try {
    await loadGoogleMaps()
    const detail = await routeSearchStore.loadDetail(routeId)
    const path = buildRoutePathFromDetail(detail)
    if (path.length < 2) {
      return
    }

    routePolyline = new google.maps.Polyline({
      path,
      geodesic: true,
      strokeColor: '#059669',
      strokeOpacity: 0.92,
      strokeWeight: 4,
      map: mapInstance,
    })

    const bounds = new google.maps.LatLngBounds()
    for (const p of path) {
      bounds.extend(p)
    }

    const stops = [...(detail.stops ?? [])].sort((a, b) => a.sequence_order - b.sequence_order)
    for (const s of stops) {
      const lat = s.latitude
      const lng = s.longitude
      if (typeof lat !== 'number' || typeof lng !== 'number') {
        continue
      }
      if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        continue
      }
      const pos = { lat, lng }
      bounds.extend(pos)
      const m = new google.maps.Marker({
        position: pos,
        map: mapInstance,
        label: {
          text: String(s.sequence_order),
          color: '#ffffff',
          fontWeight: 'bold',
          fontSize: '11px',
        },
        title: s.name ?? s.alias ?? `Parada ${s.sequence_order}`,
        zIndex: Number(google.maps.Marker.MAX_ZINDEX) + s.sequence_order,
      })
      routeMarkers.push(m)
    }

    const walkOrigin =
      routeSearchStore.filtersApplied && mapInstance ? getWalkOriginLatLng(searchStore) : null
    if (walkOrigin && stops.length > 0) {
      const nearest = nearestStopWalkFromOrigin(walkOrigin, stops)
      if (nearest) {
        const dest = nearest.position
        const directions = await getWalkingDirectionsPath(walkOrigin, dest)
        const pathToDraw =
          directions.ok && directions.path.length >= 2
            ? directions.path
            : [walkOrigin, dest]

        walkPolyline = createDottedWalkPolyline(mapInstance, pathToDraw)
        walkOriginMarker = new google.maps.Marker({
          position: walkOrigin,
          map: mapInstance,
          title: t('search.map.walkOriginTitle'),
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 9,
            fillColor: '#2563eb',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2,
          },
        })
        for (const p of pathToDraw) {
          bounds.extend(p)
        }
        const infoHtml =
          directions.ok && directions.distanceText
            ? t('search.map.walkToNearestStopDirections', {
                distance: directions.distanceText,
                duration: directions.durationText ?? '—',
              })
            : directions.ok
              ? t('search.map.walkToNearestStop', {
                  distance: formatWalkDistanceMeters(nearest.distanceM),
                })
              : `${t('search.map.walkDirectionsFallback')}: ${t('search.map.walkToNearestStop', { distance: formatWalkDistanceMeters(nearest.distanceM) })}`
        walkInfoWindow = new google.maps.InfoWindow({
          content: `<div style="font-size:12px;font-weight:600;color:#1f2937;max-width:240px;padding:2px 0;line-height:1.35">${infoHtml}</div>`,
        })
        walkInfoWindow.setPosition(dest)
        walkInfoWindow.open({ map: mapInstance })
      }
    }

    mapInstance.fitBounds(bounds, { top: 48, right: 48, bottom: 48, left: 48 })
  } catch (e) {
    console.warn('[GoogleMapContainer] route overlay', e)
  }
}

watch(
  [
    () => routeSearchStore.selectedRouteId,
    () => routeSearchStore.filtersApplied,
    () => searchStore.outboundOriginLat,
    () => searchStore.outboundOriginLng,
    () => searchStore.returnDestLat,
    () => searchStore.returnDestLng,
    () => searchStore.tripMode,
  ],
  () => {
    void paintSelectedRoute(routeSearchStore.selectedRouteId)
  },
)

onMounted(async () => {
  if (!hasGoogleMapsApiKey()) return
  if (!mapEl.value) return
  try {
    await loadGoogleMaps()
    if (!mapEl.value) return

    mapInstance = new google.maps.Map(mapEl.value, {
      center: DEFAULT_CENTER,
      zoom: DEFAULT_ZOOM,
      mapTypeControl: true,
      streetViewControl: false,
      fullscreenControl: true,
    })

    userMarker = new google.maps.Marker({
      map: mapInstance,
      position: DEFAULT_CENTER,
      title: 'Madrid (ejemplo)',
    })

    tryLocateUser(mapInstance, userMarker)

    void paintSelectedRoute(routeSearchStore.selectedRouteId)
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'No se pudo cargar el mapa'
    console.warn('[GoogleMapContainer]', e)
  }
})
</script>

<template>
  <div
    :class="['relative overflow-hidden rounded-xl bg-neutral-200 dark:bg-neutral-800', MAP_BOX]"
  >
    <div
      v-if="!hasGoogleMapsApiKey()"
      :class="[
        'flex flex-col items-center justify-center gap-2 p-6 text-center text-sm text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
        MAP_BOX,
      ]"
    >
      <p>
        Define <code class="rounded bg-neutral-100 px-1">VITE_GOOGLE_MAPS_API_KEY</code> en
        <code class="rounded bg-neutral-100 px-1">.env</code> para cargar Google Maps.
      </p>
    </div>
    <div
      v-else-if="error"
      :class="['flex items-center justify-center p-4 text-center text-sm text-red-600', MAP_BOX]"
    >
      {{ error }}
    </div>
    <div v-else ref="mapEl" :class="MAP_BOX" role="application" aria-label="Mapa" />
  </div>
</template>
