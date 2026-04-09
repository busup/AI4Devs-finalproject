import { defineStore } from 'pinia'
import { ref } from 'vue'

import { pickRecommendedRouteId, routeMatchesSearchFilters } from '@/lib/routeFilter'
import { getWalkOriginLatLng } from '@/lib/walkOriginFromSearch'
import { minWalkDistanceToRouteMeters } from '@/lib/walkToRoute'
import { fetchSchedulesBySnapshots } from '@/services/planificationsApi'
import { getRouteDetail, searchRoutes } from '@/services/routeSearcherApi'
import { useSearchStore } from '@/stores/search'
import type {
  RouteDaySchedule,
  RouteDetailResponse,
  SearchRouteResultItem,
} from '@/types'

export const useRouteSearchStore = defineStore('routeSearch', () => {
  const loading = ref(false)
  const error = ref<string | null>(null)
  /** Resultados completos del último POST search. */
  const rawResults = ref<SearchRouteResultItem[]>([])
  /** Resultados mostrados (todos o filtrados). */
  const results = ref<SearchRouteResultItem[]>([])
  const filtersApplied = ref(false)
  const recommendedRouteId = ref<string | null>(null)
  const selectedRouteId = ref<string | null>(null)
  const detailCache = ref<Record<string, RouteDetailResponse>>({})
  /** Distancia en metros (línea recta) del punto Places al stop más cercano; solo tras Filtrar con coords. */
  const walkDistanceByRouteId = ref<Record<string, number | null>>({})
  const daySchedulesBySnapshotId = ref<Record<string, RouteDaySchedule | null>>({})

  function scheduleForSnapshot(
    snapshotId: string | undefined,
  ): RouteDaySchedule | null | undefined {
    if (!snapshotId) {
      return undefined
    }
    if (!Object.prototype.hasOwnProperty.call(daySchedulesBySnapshotId.value, snapshotId)) {
      return undefined
    }
    return daySchedulesBySnapshotId.value[snapshotId]
  }

  function setSelectedRouteId(id: string | null) {
    selectedRouteId.value = id
  }

  async function load(): Promise<void> {
    loading.value = true
    error.value = null
    const searchStore = useSearchStore()
    const useMock = import.meta.env.VITE_USE_MOCK_API === 'true'
    const base = (import.meta.env.VITE_API_BASE_URL as string | undefined)?.trim()
    if (!useMock && !base) {
      error.value =
        'Falta VITE_API_BASE_URL en .env (ej. http://localhost:8001/api/v1 o /dev-ms-router/api/v1 con proxy Vite).'
      loading.value = false
      return
    }

    daySchedulesBySnapshotId.value = {}
    filtersApplied.value = false
    recommendedRouteId.value = null
    selectedRouteId.value = null
    walkDistanceByRouteId.value = {}

    try {
      const res = await searchRoutes({
        origin: { latitude: 41.3874, longitude: 2.1686 },
        destination: { latitude: 41.4036, longitude: 2.1744 },
        date: searchStore.departureDate,
        journeyType: 'outbound',
      })
      const list = Array.isArray(res.results) ? res.results : []
      /** Copias separadas: si `results` y `rawResults` comparten array, un filtro podría mutar la “fuente de verdad”. */
      rawResults.value = list.slice()
      results.value = list.slice()

      const snapshotIds = [
        ...new Set(
          res.results
            .map((r) => r.snapshotId)
            .filter((id): id is string => typeof id === 'string' && id.length > 0),
        ),
      ]

      if (!useMock && snapshotIds.length > 0) {
        try {
          const sched = await fetchSchedulesBySnapshots({
            date: searchStore.departureDate,
            routeSnapshotIds: snapshotIds,
          })
          const next: Record<string, RouteDaySchedule | null> = {}
          for (const id of snapshotIds) {
            next[id] = sched.schedules[id] ?? null
          }
          daySchedulesBySnapshotId.value = next
        } catch (e) {
          console.warn('[routeSearch] ms-planifications:', e)
          const next: Record<string, RouteDaySchedule | null> = {}
          for (const id of snapshotIds) {
            next[id] = null
          }
          daySchedulesBySnapshotId.value = next
        }
      }
    } catch (e) {
      error.value = e instanceof Error ? e.message : String(e)
      rawResults.value = []
      results.value = []
    } finally {
      loading.value = false
    }
  }

  async function loadDetail(routeId: string): Promise<RouteDetailResponse> {
    const cached = detailCache.value[routeId]
    if (cached) {
      return cached
    }
    const d = await getRouteDetail(routeId)
    detailCache.value = { ...detailCache.value, [routeId]: d }
    return d
  }

  async function applyRouteFilters(): Promise<void> {
    const searchStore = useSearchStore()
    filtersApplied.value = true
    recommendedRouteId.value = null

    if (rawResults.value.length === 0) {
      results.value = []
      walkDistanceByRouteId.value = {}
      return
    }

    const next: SearchRouteResultItem[] = []
    for (const r of rawResults.value) {
      const rid = String(r.routeId)
      try {
        const detail = await loadDetail(rid)
        if (
          routeMatchesSearchFilters(detail, r, {
            tripMode: searchStore.tripMode,
            outboundDestStopId: searchStore.outboundDestStopId,
            returnOriginStopId: searchStore.returnOriginStopId,
            outboundTimeFilter: searchStore.outboundTimeFilter,
            returnTimeFilter: searchStore.returnTimeFilter,
            scheduleForSnapshot,
          })
        ) {
          next.push(r)
        }
      } catch {
        /* omitir ruta si falla el detalle */
      }
    }

    results.value = next
    recommendedRouteId.value = pickRecommendedRouteId(next, scheduleForSnapshot)

    const origin = getWalkOriginLatLng(searchStore)
    const walkMap: Record<string, number | null> = {}
    if (origin && next.length > 0) {
      for (const r of next) {
        const rid = String(r.routeId)
        try {
          const detail = await loadDetail(rid)
          walkMap[rid] = minWalkDistanceToRouteMeters(origin, detail.stops ?? [])
        } catch {
          walkMap[rid] = null
        }
      }
    }
    walkDistanceByRouteId.value = walkMap
  }

  function resetAfterDateChange() {
    detailCache.value = {}
    selectedRouteId.value = null
    walkDistanceByRouteId.value = {}
  }

  /** Vuelve a mostrar todas las rutas del último POST sin criterios de filtro cliente. */
  function clearAppliedFilters() {
    const snapshot = rawResults.value.slice()
    filtersApplied.value = false
    recommendedRouteId.value = null
    walkDistanceByRouteId.value = {}
    results.value = snapshot
    const ids = new Set(snapshot.map((r) => String(r.routeId)))
    if (selectedRouteId.value && !ids.has(selectedRouteId.value)) {
      selectedRouteId.value = null
    }
  }

  return {
    loading,
    error,
    rawResults,
    results,
    filtersApplied,
    recommendedRouteId,
    selectedRouteId,
    detailCache,
    walkDistanceByRouteId,
    daySchedulesBySnapshotId,
    scheduleForSnapshot,
    setSelectedRouteId,
    load,
    loadDetail,
    applyRouteFilters,
    clearAppliedFilters,
    resetAfterDateChange,
  }
})
