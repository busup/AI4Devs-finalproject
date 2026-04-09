<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import { stopsToItineraryRows, useSearchRoutes } from '@/composables/useSearchRoutes'
import { getWalkOriginLatLng } from '@/lib/walkOriginFromSearch'
import { useSearchStore } from '@/stores/search'
import type { RouteStopDetail } from '@/types'

const { t } = useI18n()
const searchStore = useSearchStore()

const {
  loading,
  error,
  results,
  load,
  loadDetail,
  scheduleForSnapshot,
  recommendedRouteId,
  walkDistanceByRouteId,
  filtersApplied,
  selectedRouteId,
  setSelectedRouteId,
} = useSearchRoutes()

const mockMode = import.meta.env.VITE_USE_MOCK_API === 'true'

const walkOriginPresent = computed(() => getWalkOriginLatLng(searchStore) !== null)

/** Si hay dirección con coords y alguna ruta queda a < 1 km, solo esas llevan badge; si no, se usa la recomendada por horario. */
const anyWalkUnder1km = computed(() => {
  if (!filtersApplied.value || !walkOriginPresent.value) {
    return false
  }
  for (const r of results.value) {
    const d = walkDistanceByRouteId.value[String(r.routeId)]
    if (typeof d === 'number' && d < 1000) {
      return true
    }
  }
  return false
})

type RouteItem = {
  id: string
  snapshotId?: string
  title: string
  line: string
  dep: string
  arr: string
  depMuted?: boolean
  arrMuted?: boolean
  noSchedule?: boolean
  recommended?: boolean
}

type ItineraryRow =
  | { kind: 'stop'; time: string; place: string; detail?: string }
  | { kind: 'segment'; label: string; dashed?: boolean }

const displayRoutes = computed<RouteItem[]>(() =>
  results.value.map((r) => {
    const sid = r.snapshotId ? String(r.snapshotId) : undefined
    const sched = sid ? scheduleForSnapshot(sid) : undefined
    const dep = sched?.departureTime ?? '—'
    const arr = sched?.arrivalTime ?? '—'
    const settled = !loading.value
    const hasTimes = !!(sched?.departureTime && sched?.arrivalTime)
    const noSchedule = settled && !!sid && sched === null
    const rid = String(r.routeId)
    const walkM = walkDistanceByRouteId.value[rid]
    const walkBadge =
      filtersApplied.value &&
      walkOriginPresent.value &&
      typeof walkM === 'number' &&
      walkM < 1000
    const timeBadge =
      filtersApplied.value &&
      recommendedRouteId.value === rid &&
      (!walkOriginPresent.value || !anyWalkUnder1km.value)

    return {
      id: rid,
      snapshotId: sid,
      title: r.title,
      line: r.assignedStop ? `${r.assignedStop.name} → …` : t('search.list.routeLineFallback'),
      dep,
      arr,
      depMuted: !hasTimes,
      arrMuted: !hasTimes,
      noSchedule,
      recommended: walkBadge || timeBadge,
    }
  }),
)

watch(
  () => searchStore.departureDate,
  () => {
    itineraryOpen.value = {}
    itineraryRows.value = {}
    itineraryLoading.value = {}
  },
)
const itineraryOpen = ref<Record<string, boolean>>({})
const itineraryRows = ref<Record<string, ItineraryRow[]>>({})
const itineraryLoading = ref<Record<string, boolean>>({})

function selectRoute(id: string) {
  setSelectedRouteId(id)
}

function isItineraryOpen(id: string): boolean {
  return !!itineraryOpen.value[id]
}

async function toggleItinerary(id: string, snapshotId?: string) {
  const next = !itineraryOpen.value[id]
  itineraryOpen.value = { ...itineraryOpen.value, [id]: next }
  if (!next) {
    return
  }
  if (itineraryRows.value[id]?.length) {
    return
  }
  itineraryLoading.value = { ...itineraryLoading.value, [id]: true }
  try {
    const detail = await loadDetail(id)
    const stops: RouteStopDetail[] = detail.stops ?? []
    const day =
      snapshotId != null && snapshotId !== ''
        ? scheduleForSnapshot(snapshotId)
        : undefined
    itineraryRows.value = {
      ...itineraryRows.value,
      [id]: stopsToItineraryRows(stops, day === undefined ? null : day),
    }
  } catch {
    itineraryRows.value = {
      ...itineraryRows.value,
      [id]: [{ kind: 'stop', time: '—', place: t('search.list.itineraryError'), detail: undefined }],
    }
  } finally {
    itineraryLoading.value = { ...itineraryLoading.value, [id]: false }
  }
}

function itineraryFor(id: string): ItineraryRow[] {
  return itineraryRows.value[id] ?? []
}
</script>

<template>
  <div class="flex min-h-0 flex-1 flex-col">
    <div class="mb-4 flex shrink-0 items-center justify-between">
      <div>
        <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">
          {{ t('search.list.titleOutbound') }}
        </h2>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-neutral-500 dark:text-neutral-400">
          <span class="font-semibold text-neutral-700 dark:text-neutral-200">{{ displayRoutes.length }}</span>
          {{ t('search.list.available') }}
        </span>
        <button
          type="button"
          class="flex items-center gap-1 text-sm font-medium text-emerald-500 transition-colors hover:text-emerald-600 dark:text-emerald-400 dark:hover:text-emerald-300"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
          </svg>
          {{ t('search.list.morningFilter') }}
        </button>
      </div>
    </div>

    <p v-if="loading" class="text-sm text-neutral-500 dark:text-neutral-400">
      {{ t('search.list.loading') }}
    </p>
    <p v-else-if="error" class="text-sm text-red-600 dark:text-red-400">
      {{ error }}
      <button
        type="button"
        class="ml-2 underline"
        @click="load"
      >
        {{ t('search.list.retry') }}
      </button>
    </p>
    <p
      v-else-if="!displayRoutes.length && mockMode"
      class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-100"
    >
      {{ t('search.list.emptyMock') }}
    </p>
    <p
      v-else-if="!displayRoutes.length && filtersApplied"
      class="text-sm text-neutral-500 dark:text-neutral-400"
    >
      {{ t('search.list.emptyAfterFilter') }}
    </p>
    <p
      v-else-if="!displayRoutes.length"
      class="text-sm text-neutral-500 dark:text-neutral-400"
    >
      {{ t('search.list.empty') }}
    </p>

    <div class="min-h-0 flex-1 overflow-y-auto overflow-x-visible pt-2 pr-1">
      <div class="flex flex-col gap-4">
        <article
          v-for="route in displayRoutes"
          :key="route.id"
          role="button"
          tabindex="0"
          :aria-pressed="selectedRouteId === route.id"
          :aria-label="`${route.title}. ${t('search.list.selectRouteHint')}`"
          class="relative cursor-pointer rounded-xl bg-white p-5 shadow-sm outline-none transition-colors focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:bg-neutral-900 dark:shadow-none dark:focus-visible:ring-emerald-400 dark:focus-visible:ring-offset-neutral-950"
          :class="
            selectedRouteId === route.id
              ? 'border-2 border-emerald-500 dark:border-emerald-400'
              : 'border border-neutral-200 hover:border-neutral-300 dark:border-neutral-700 dark:hover:border-neutral-600'
          "
          @click="selectRoute(route.id)"
          @keydown.enter.prevent="selectRoute(route.id)"
          @keydown.space.prevent="selectRoute(route.id)"
        >
          <span
            v-if="route.recommended"
            class="absolute right-4 top-4 rounded-full bg-emerald-500 px-3 py-1 text-xs font-semibold uppercase leading-tight text-white"
          >
            {{ t('search.list.recommended') }}
          </span>

          <h3
            class="text-lg font-semibold text-neutral-800 dark:text-neutral-100"
            :class="route.recommended ? 'pr-36' : ''"
          >
            {{ route.title }}
          </h3>
          <p class="mb-3 text-sm text-neutral-500 dark:text-neutral-400">
            {{ route.line }}
          </p>
          <p
            v-if="route.noSchedule"
            class="mb-2 text-xs text-neutral-500 dark:text-neutral-400"
          >
            {{ t('search.list.noScheduleThisDay') }}
          </p>

          <div class="mb-4 flex items-center gap-4">
            <div class="flex flex-col">
              <span
                class="text-2xl font-bold"
                :class="
                  route.depMuted
                    ? 'text-neutral-400 dark:text-neutral-500'
                    : 'text-neutral-800 dark:text-neutral-100'
                "
              >
                {{ route.dep }}
              </span>
              <span class="text-xs uppercase text-neutral-500 dark:text-neutral-400">{{
                t('search.list.departure')
              }}</span>
            </div>
            <div class="flex flex-1 items-center gap-2">
              <div class="flex-1 border-t-2 border-dashed border-neutral-300 dark:border-neutral-600" />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="text-neutral-400 dark:text-neutral-500"
                aria-hidden="true"
              >
                <path d="M8 6v6" />
                <path d="M15 6v6" />
                <path d="M2 12h19.6" />
                <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                <circle cx="7" cy="18" r="2" />
                <path d="M9 18h5" />
                <circle cx="16" cy="18" r="2" />
              </svg>
              <div class="flex-1 border-t-2 border-dashed border-neutral-300 dark:border-neutral-600" />
            </div>
            <div class="flex flex-col text-right">
              <span
                class="text-2xl font-bold"
                :class="
                  route.arrMuted
                    ? 'text-neutral-400 dark:text-neutral-500'
                    : 'text-neutral-800 dark:text-neutral-100'
                "
              >
                {{ route.arr }}
              </span>
              <span class="text-xs uppercase text-neutral-500 dark:text-neutral-400">{{
                t('search.list.arrival')
              }}</span>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="text-neutral-400 dark:text-neutral-500"
                aria-hidden="true"
              >
                <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                <line x1="12" x2="12.01" y1="20" y2="20" />
              </svg>
              <svg
                v-if="selectedRouteId === route.id"
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="text-emerald-500 dark:text-emerald-400"
                aria-hidden="true"
              >
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
              </svg>
            </div>

            <div
              v-if="selectedRouteId === route.id"
              class="flex items-center gap-2 text-sm font-semibold text-emerald-500 dark:text-emerald-400"
            >
              {{ t('search.list.selected') }}
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
            </div>
            <span v-else class="text-sm font-semibold uppercase text-neutral-400 dark:text-neutral-500">
              {{ t('search.list.tapToSelect') }}
            </span>
          </div>

          <button
            type="button"
            class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-neutral-600 transition-colors hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200"
            :aria-expanded="isItineraryOpen(route.id)"
            :aria-label="t('search.list.itineraryToggle')"
            @click.stop="toggleItinerary(route.id, route.snapshotId)"
            @keydown.space.stop
          >
            {{ t('search.list.itinerary') }}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="shrink-0 transition-transform"
              :class="isItineraryOpen(route.id) ? 'rotate-180' : ''"
              aria-hidden="true"
            >
              <path d="m6 9 6 6 6-6" />
            </svg>
          </button>

          <div
            v-if="isItineraryOpen(route.id) && itineraryLoading[route.id]"
            class="mt-3 border-t border-neutral-200 pt-3 text-sm text-neutral-500 dark:border-neutral-700 dark:text-neutral-400"
          >
            {{ t('search.list.itineraryLoading') }}
          </div>

          <div
            v-else-if="isItineraryOpen(route.id) && itineraryFor(route.id).length"
            class="mt-3 border-t border-neutral-200 pt-3 dark:border-neutral-700"
            role="region"
            :aria-label="t('search.list.itinerary')"
            @click.stop
          >
            <ul class="space-y-3">
              <li
                v-for="(row, idx) in itineraryFor(route.id)"
                :key="idx"
                class="flex gap-3 text-sm"
              >
                <template v-if="row.kind === 'stop'">
                  <span
                    class="w-14 shrink-0 pt-0.5 text-right font-semibold text-neutral-800 dark:text-neutral-100"
                    >{{ row.time }}</span
                  >
                  <span
                    class="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-neutral-900"
                    aria-hidden="true"
                  />
                  <div class="min-w-0 flex-1">
                    <p class="font-medium text-neutral-800 dark:text-neutral-100">{{ row.place }}</p>
                    <p v-if="row.detail" class="text-xs text-neutral-500 dark:text-neutral-400">
                      {{ row.detail }}
                    </p>
                  </div>
                </template>
                <template v-else>
                  <span class="w-14 shrink-0" />
                  <div class="flex w-6 shrink-0 justify-center pt-0.5" aria-hidden="true">
                    <div
                      class="min-h-[1.25rem] border-l-2 border-neutral-300 dark:border-neutral-600"
                      :class="row.dashed ? 'border-dashed' : 'border-solid'"
                    />
                  </div>
                  <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ row.label }}</p>
                </template>
              </li>
            </ul>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>
