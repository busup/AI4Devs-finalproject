import { storeToRefs } from 'pinia'

import { useRouteSearchStore } from '@/stores/routeSearch'
import type { RouteDaySchedule, RouteStopDetail } from '@/types'

/**
 * Estado de búsqueda de rutas (singleton vía Pinia).
 * La carga inicial debe dispararse desde la vista (`routeSearchStore.load()`).
 */
export function useSearchRoutes() {
  const store = useRouteSearchStore()
  return {
    ...storeToRefs(store),
    load: () => store.load(),
    loadDetail: (id: string) => store.loadDetail(id),
    scheduleForSnapshot: (sid: string | undefined) => store.scheduleForSnapshot(sid),
    setSelectedRouteId: (id: string | null) => store.setSelectedRouteId(id),
  }
}

/** Ordena paradas y construye filas de itinerario; horas desde ms-planifications si existen. */
export function stopsToItineraryRows(
  stops: RouteStopDetail[],
  daySchedule: RouteDaySchedule | null | undefined,
) {
  const timeByStopId = new Map<string, string>()
  if (daySchedule?.stops?.length) {
    for (const s of daySchedule.stops) {
      timeByStopId.set(String(s.stopId), s.scheduledTime)
    }
  }

  const sorted = [...stops].sort((a, b) => a.sequence_order - b.sequence_order)
  const rows: Array<
    | { kind: 'stop'; time: string; place: string; detail?: string }
    | { kind: 'segment'; label: string; dashed?: boolean }
  > = []
  sorted.forEach((s, idx) => {
    const t = timeByStopId.get(String(s.stop_id))
    rows.push({
      kind: 'stop',
      time: t ?? '—',
      place: s.name ?? s.alias ?? 'Parada',
      detail: s.alias ?? undefined,
    })
    if (idx < sorted.length - 1) {
      rows.push({ kind: 'segment', label: 'En ruta' })
    }
  })
  return rows
}
