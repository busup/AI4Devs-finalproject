<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import GooglePlacesInput from '@/components/search/GooglePlacesInput.vue'
import SearchDateField from '@/components/search/SearchDateField.vue'
import SearchTimeField from '@/components/search/SearchTimeField.vue'
import StopSelect from '@/components/search/StopSelect.vue'
import { useRouteSearchStore } from '@/stores/routeSearch'
import { useSearchStore } from '@/stores/search'
import type { TripMode } from '@/types/searchUi'

const { t } = useI18n()
const store = useSearchStore()
const routeSearchStore = useRouteSearchStore()
const filterBusy = ref(false)
const {
  tripMode,
  outboundOriginAddress,
  outboundDestStopId,
  returnOriginStopId,
  returnDestAddress,
} = storeToRefs(store)
/** Lectura directa del store en computed: evita fallos de reactividad con `storeToRefs` en stores setup. */
const canClearFilters = computed(() => routeSearchStore.filtersApplied)

function selectTripMode(m: TripMode) {
  store.setTripMode(m)
}

async function onApplyFilters() {
  filterBusy.value = true
  try {
    await routeSearchStore.applyRouteFilters()
  } finally {
    filterBusy.value = false
  }
}

function onClearFilters() {
  routeSearchStore.clearAppliedFilters()
}
</script>

<template>
  <div
    class="border-b border-neutral-200 bg-white shadow-sm transition-colors dark:border-neutral-800 dark:bg-neutral-900 dark:shadow-none"
  >
    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
      <div
        class="mb-5 flex flex-wrap items-center gap-2 rounded-full bg-neutral-100 p-1 dark:bg-neutral-800/90"
      >
        <button
          type="button"
          class="rounded-full px-4 py-2 text-sm font-medium transition-colors sm:px-5"
          :class="
            tripMode === 'outbound_only'
              ? 'bg-white text-emerald-600 shadow-sm dark:bg-neutral-900 dark:text-emerald-400 dark:shadow-md'
              : 'text-neutral-600 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200'
          "
          @click="selectTripMode('outbound_only')"
        >
          {{ t('search.trip.outboundOnly') }}
        </button>
        <button
          type="button"
          class="rounded-full px-4 py-2 text-sm transition-colors sm:px-5"
          :class="
            tripMode === 'return_only'
              ? 'bg-white text-emerald-600 shadow-sm dark:bg-neutral-900 dark:text-emerald-400 dark:shadow-md'
              : 'text-neutral-600 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200'
          "
          @click="selectTripMode('return_only')"
        >
          {{ t('search.trip.returnOnly') }}
        </button>
        <button
          type="button"
          class="rounded-full px-4 py-2 text-sm transition-colors sm:px-5"
          :class="
            tripMode === 'roundtrip'
              ? 'bg-white text-emerald-600 shadow-sm dark:bg-neutral-900 dark:text-emerald-400 dark:shadow-md'
              : 'text-neutral-600 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200'
          "
          @click="selectTripMode('roundtrip')"
        >
          {{ t('search.trip.roundtrip') }}
        </button>
      </div>

      <!-- Solo ida -->
      <div v-if="tripMode === 'outbound_only'" class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
        <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
          <label class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400" for="out-origin">
            {{ t('search.fields.origin') }}
          </label>
          <GooglePlacesInput
            v-model="outboundOriginAddress"
            input-id="out-origin"
            @coords-change="store.setOutboundOriginCoords"
          />
        </div>
        <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
          <label class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400" for="out-dest">
            {{ t('search.fields.destination') }}
          </label>
          <StopSelect v-model="outboundDestStopId" select-id="out-dest" />
        </div>
        <SearchDateField />
        <SearchTimeField leg="outbound" />
        <div
          class="flex w-full shrink-0 flex-wrap items-end justify-end gap-2 lg:w-auto lg:min-w-[220px]"
        >
          <button
            type="button"
            class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg border border-neutral-300 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm transition-colors hover:border-neutral-400 hover:bg-neutral-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:border-neutral-500 dark:hover:bg-neutral-700"
            :disabled="!canClearFilters || routeSearchStore.loading || filterBusy"
            @click="onClearFilters"
          >
            {{ t('search.filter.clear') }}
          </button>
          <button
            type="button"
            class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-emerald-300 dark:disabled:bg-emerald-800"
            :disabled="routeSearchStore.loading || filterBusy"
            @click="onApplyFilters"
          >
            {{ t('search.filter.apply') }}
          </button>
        </div>
      </div>

      <!-- Solo vuelta -->
      <div v-else-if="tripMode === 'return_only'" class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
        <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
          <label class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400" for="ret-origin">
            {{ t('search.fields.origin') }}
          </label>
          <StopSelect v-model="returnOriginStopId" select-id="ret-origin" />
        </div>
        <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
          <label class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400" for="ret-dest">
            {{ t('search.fields.destination') }}
          </label>
          <GooglePlacesInput
            v-model="returnDestAddress"
            input-id="ret-dest"
            @coords-change="store.setReturnDestCoords"
          />
        </div>
        <SearchDateField />
        <SearchTimeField leg="return" />
        <div
          class="flex w-full shrink-0 flex-wrap items-end justify-end gap-2 lg:w-auto lg:min-w-[220px]"
        >
          <button
            type="button"
            class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg border border-neutral-300 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm transition-colors hover:border-neutral-400 hover:bg-neutral-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:border-neutral-500 dark:hover:bg-neutral-700"
            :disabled="!canClearFilters || routeSearchStore.loading || filterBusy"
            @click="onClearFilters"
          >
            {{ t('search.filter.clear') }}
          </button>
          <button
            type="button"
            class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-emerald-300 dark:disabled:bg-emerald-800"
            :disabled="routeSearchStore.loading || filterBusy"
            @click="onApplyFilters"
          >
            {{ t('search.filter.apply') }}
          </button>
        </div>
      </div>

      <!-- Ida y vuelta -->
      <div v-else class="flex flex-col gap-6">
        <section aria-labelledby="leg-outbound-heading">
          <h3 id="leg-outbound-heading" class="mb-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            {{ t('search.legs.outbound') }}
          </h3>
          <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
            <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
              <label
                class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
                for="rt-out-origin"
              >
                {{ t('search.fields.origin') }}
              </label>
              <GooglePlacesInput
                v-model="outboundOriginAddress"
                input-id="rt-out-origin"
                @coords-change="store.setOutboundOriginCoords"
              />
            </div>
            <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
              <label
                class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
                for="rt-out-dest"
              >
                {{ t('search.fields.destination') }}
              </label>
              <StopSelect v-model="outboundDestStopId" select-id="rt-out-dest" />
            </div>
          </div>
        </section>
        <section aria-labelledby="leg-return-heading">
          <h3 id="leg-return-heading" class="mb-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            {{ t('search.legs.return') }}
          </h3>
          <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
            <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
              <label
                class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
                for="rt-ret-origin"
              >
                {{ t('search.fields.origin') }}
              </label>
              <StopSelect v-model="returnOriginStopId" select-id="rt-ret-origin" />
            </div>
            <div class="flex min-w-0 flex-1 flex-col lg:min-w-[200px]">
              <label
                class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
                for="rt-ret-dest"
              >
                {{ t('search.fields.destination') }}
              </label>
              <GooglePlacesInput
                v-model="returnDestAddress"
                input-id="rt-ret-dest"
                @coords-change="store.setReturnDestCoords"
              />
            </div>
          </div>
        </section>
        <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
          <SearchDateField />
          <SearchTimeField leg="outbound" />
          <SearchTimeField leg="return" />
          <div
            class="flex w-full shrink-0 flex-wrap items-end justify-end gap-2 lg:w-auto lg:min-w-[220px]"
          >
            <button
              type="button"
              class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg border border-neutral-300 bg-white px-4 text-sm font-semibold text-neutral-700 shadow-sm transition-colors hover:border-neutral-400 hover:bg-neutral-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:border-neutral-500 dark:hover:bg-neutral-700"
              :disabled="!canClearFilters || routeSearchStore.loading || filterBusy"
              @click="onClearFilters"
            >
              {{ t('search.filter.clear') }}
            </button>
            <button
              type="button"
              class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-emerald-300 dark:disabled:bg-emerald-800"
              :disabled="routeSearchStore.loading || filterBusy"
              @click="onApplyFilters"
            >
              {{ t('search.filter.apply') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
