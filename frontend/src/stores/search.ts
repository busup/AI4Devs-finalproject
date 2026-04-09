import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

import { getRouteTerminalStops } from '@/services/routeSearcherApi'
import type { RouteTerminalStopItem } from '@/types'
import type { TimeFilter, TripMode } from '@/types/searchUi'

function todayISO(): string {
  const d = new Date()
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

export const useSearchStore = defineStore('search', () => {
  const tripMode = ref<TripMode>('outbound_only')

  /** Tramo ida: origen dirección (Google), destino parada */
  const outboundOriginAddress = ref('')
  const outboundOriginLat = ref<number | null>(null)
  const outboundOriginLng = ref<number | null>(null)
  const outboundDestStopId = ref('')

  /** Tramo vuelta: origen parada, destino dirección (Google) */
  const returnOriginStopId = ref('')
  const returnDestAddress = ref('')
  const returnDestLat = ref<number | null>(null)
  const returnDestLng = ref<number | null>(null)

  /** Últimas paradas de rutas aprobadas (GET /stops/route-terminals). */
  const terminalStops = ref<RouteTerminalStopItem[]>([])
  const terminalStopsLoading = ref(false)

  /** Fecha de salida (YYYY-MM-DD) */
  const departureDate = ref(todayISO())

  /** Restricción de horario en tramo de ida (solo ida / ida y vuelta). */
  const outboundTimeFilter = ref<TimeFilter | null>(null)
  /** Restricción de horario en tramo de vuelta (solo vuelta / ida y vuelta). */
  const returnTimeFilter = ref<TimeFilter | null>(null)

  function setTripMode(m: TripMode) {
    tripMode.value = m
  }

  function setOutboundTimeFilter(v: TimeFilter | null) {
    outboundTimeFilter.value = v
  }

  function setReturnTimeFilter(v: TimeFilter | null) {
    returnTimeFilter.value = v
  }

  function setOutboundOriginCoords(c: { lat: number; lng: number } | null) {
    if (c && Number.isFinite(c.lat) && Number.isFinite(c.lng)) {
      outboundOriginLat.value = c.lat
      outboundOriginLng.value = c.lng
    } else {
      outboundOriginLat.value = null
      outboundOriginLng.value = null
    }
  }

  function setReturnDestCoords(c: { lat: number; lng: number } | null) {
    if (c && Number.isFinite(c.lat) && Number.isFinite(c.lng)) {
      returnDestLat.value = c.lat
      returnDestLng.value = c.lng
    } else {
      returnDestLat.value = null
      returnDestLng.value = null
    }
  }

  const departureDateDisplay = computed(() => {
    try {
      const [y, mo, d] = departureDate.value.split('-').map(Number)
      return new Intl.DateTimeFormat('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      }).format(new Date(y, mo - 1, d))
    } catch {
      return departureDate.value
    }
  })

  function formatTimeFilterLine(tf: TimeFilter | null): string {
    if (!tf) return ''
    const label =
      tf.mode === 'departure'
        ? 'Salida después de'
        : 'Llegada antes de'
    return `${label} ${tf.hhmm}`
  }

  const outboundTimeFilterDisplay = computed(() => formatTimeFilterLine(outboundTimeFilter.value))
  const returnTimeFilterDisplay = computed(() => formatTimeFilterLine(returnTimeFilter.value))

  async function loadTerminalStops(): Promise<void> {
    terminalStopsLoading.value = true
    try {
      const { stops } = await getRouteTerminalStops()
      terminalStops.value = stops
      const ids = new Set(stops.map((s) => s.stopId))
      if (stops.length > 0) {
        if (!outboundDestStopId.value || !ids.has(outboundDestStopId.value)) {
          outboundDestStopId.value = stops[0].stopId
        }
        if (!returnOriginStopId.value || !ids.has(returnOriginStopId.value)) {
          returnOriginStopId.value = stops[0].stopId
        }
      }
    } catch {
      terminalStops.value = []
    } finally {
      terminalStopsLoading.value = false
    }
  }

  return {
    tripMode,
    outboundOriginAddress,
    outboundOriginLat,
    outboundOriginLng,
    outboundDestStopId,
    returnOriginStopId,
    returnDestAddress,
    returnDestLat,
    returnDestLng,
    terminalStops,
    terminalStopsLoading,
    loadTerminalStops,
    departureDate,
    outboundTimeFilter,
    returnTimeFilter,
    setTripMode,
    setOutboundTimeFilter,
    setReturnTimeFilter,
    setOutboundOriginCoords,
    setReturnDestCoords,
    departureDateDisplay,
    outboundTimeFilterDisplay,
    returnTimeFilterDisplay,
  }
})
