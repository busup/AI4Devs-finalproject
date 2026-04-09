import type { RouteDaySchedule } from '@/types'
import type { RouteDetailResponse } from '@/types'
import type { SearchRouteResultItem } from '@/types'
import type { TimeFilter, TripMode } from '@/types/searchUi'

function stopIdsOnRoute(detail: RouteDetailResponse): Set<string> {
  const ids = new Set<string>()
  for (const s of detail.stops ?? []) {
    ids.add(String(s.stop_id))
  }
  return ids
}

function hhmmToMinutes(hhmm: string): number {
  const [h, m] = hhmm.split(':').map((x) => parseInt(x, 10))
  if (Number.isNaN(h) || Number.isNaN(m)) {
    return 0
  }
  return h * 60 + m
}

/**
 * Filtro horario en tramo de ida: solo `departure` (salida después de) por ahora.
 */
function matchesOutboundTimeFilter(
  schedule: RouteDaySchedule | null | undefined,
  filter: TimeFilter | null,
): boolean {
  if (!filter) {
    return true
  }
  if (filter.mode !== 'departure') {
    return true
  }
  if (!schedule?.departureTime) {
    return false
  }
  return hhmmToMinutes(schedule.departureTime) >= hhmmToMinutes(filter.hhmm)
}

function matchesReturnTimeFilter(
  schedule: RouteDaySchedule | null | undefined,
  filter: TimeFilter | null,
): boolean {
  if (!filter) {
    return true
  }
  if (filter.mode !== 'departure') {
    return true
  }
  if (!schedule?.departureTime) {
    return false
  }
  return hhmmToMinutes(schedule.departureTime) >= hhmmToMinutes(filter.hhmm)
}

export function routeMatchesSearchFilters(
  detail: RouteDetailResponse,
  item: SearchRouteResultItem,
  ctx: {
    tripMode: TripMode
    outboundDestStopId: string
    returnOriginStopId: string
    outboundTimeFilter: TimeFilter | null
    returnTimeFilter: TimeFilter | null
    scheduleForSnapshot: (snapshotId: string | undefined) => RouteDaySchedule | null | undefined
  },
): boolean {
  const ids = stopIdsOnRoute(detail)
  const snap = item.snapshotId

  if (ctx.tripMode === 'outbound_only') {
    if (ctx.outboundDestStopId && !ids.has(ctx.outboundDestStopId)) {
      return false
    }
    const sched = ctx.scheduleForSnapshot(snap)
    return matchesOutboundTimeFilter(sched === undefined ? null : sched, ctx.outboundTimeFilter)
  }

  if (ctx.tripMode === 'return_only') {
    if (ctx.returnOriginStopId && !ids.has(ctx.returnOriginStopId)) {
      return false
    }
    const sched = ctx.scheduleForSnapshot(snap)
    return matchesReturnTimeFilter(sched === undefined ? null : sched, ctx.returnTimeFilter)
  }

  // roundtrip
  if (ctx.outboundDestStopId && !ids.has(ctx.outboundDestStopId)) {
    return false
  }
  if (ctx.returnOriginStopId && !ids.has(ctx.returnOriginStopId)) {
    return false
  }
  const sched = ctx.scheduleForSnapshot(snap)
  if (!matchesOutboundTimeFilter(sched === undefined ? null : sched, ctx.outboundTimeFilter)) {
    return false
  }
  if (!matchesReturnTimeFilter(sched === undefined ? null : sched, ctx.returnTimeFilter)) {
    return false
  }
  return true
}

/**
 * Una sola ruta recomendada: menor hora de salida del día; empate → menor `routeId` lexicográfico.
 */
export function pickRecommendedRouteId(
  candidates: SearchRouteResultItem[],
  scheduleForSnapshot: (snapshotId: string | undefined) => RouteDaySchedule | null | undefined,
): string | null {
  if (candidates.length <= 1) {
    return null
  }

  type Row = { routeId: string; dep: string | null }
  const rows: Row[] = []
  for (const c of candidates) {
    const sid = c.snapshotId
    const sched = sid ? scheduleForSnapshot(sid) : undefined
    const dep = sched?.departureTime ?? null
    rows.push({ routeId: String(c.routeId), dep })
  }

  const withTime = rows.filter((r) => r.dep != null)
  if (withTime.length === 0) {
    return null
  }

  withTime.sort((a, b) => {
    const cmp = hhmmToMinutes(a.dep!) - hhmmToMinutes(b.dep!)
    if (cmp !== 0) {
      return cmp
    }
    return a.routeId.localeCompare(b.routeId)
  })

  return withTime[0].routeId
}
