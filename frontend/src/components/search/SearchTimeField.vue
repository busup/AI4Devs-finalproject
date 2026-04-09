<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'

import type { TimeFilter, TimeFilterMode } from '@/types/searchUi'
import { useSearchStore } from '@/stores/search'

const props = withDefaults(
  defineProps<{
    /** Tramo al que aplica este filtro (en ida y vuelta hay dos campos). */
    leg?: 'outbound' | 'return'
  }>(),
  { leg: 'outbound' },
)

const { t } = useI18n()
const store = useSearchStore()
const { outboundTimeFilter, returnTimeFilter, tripMode } = storeToRefs(store)

const open = ref(false)
const rootEl = ref<HTMLElement | null>(null)
const mode = ref<TimeFilterMode>('departure')
const hhmm = ref('09:00')

const currentFilter = computed<TimeFilter | null>(() =>
  props.leg === 'outbound' ? outboundTimeFilter.value : returnTimeFilter.value,
)

const displayText = computed(() => {
  const tf = currentFilter.value
  if (!tf) return ''
  const label =
    tf.mode === 'departure'
      ? t('search.time.afterDeparture')
      : t('search.time.beforeArrival')
  return `${label} ${tf.hhmm}`
})

const fieldLabel = computed(() => {
  if (tripMode.value === 'roundtrip') {
    return props.leg === 'outbound'
      ? t('search.fields.timeOutbound')
      : t('search.fields.timeReturn')
  }
  return t('search.fields.time')
})

/** Opciones HH:mm cada 30 min; incluye la hora actual si no coincide (p. ej. datos antiguos). */
const timeSlots = computed(() => {
  const slots: string[] = []
  for (let h = 0; h < 24; h++) {
    for (const m of [0, 30]) {
      slots.push(`${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`)
    }
  }
  if (hhmm.value && !slots.includes(hhmm.value)) {
    slots.push(hhmm.value)
    slots.sort()
  }
  return slots
})

watch(open, (v) => {
  if (v && currentFilter.value) {
    mode.value = currentFilter.value.mode
    hhmm.value = currentFilter.value.hhmm
  } else if (v && !currentFilter.value) {
    mode.value = 'departure'
    hhmm.value = '09:00'
  }
})

function save() {
  const payload: TimeFilter = { mode: mode.value, hhmm: hhmm.value }
  if (props.leg === 'outbound') {
    store.setOutboundTimeFilter(payload)
  } else {
    store.setReturnTimeFilter(payload)
  }
  open.value = false
}

function clearFilter() {
  if (props.leg === 'outbound') {
    store.setOutboundTimeFilter(null)
  } else {
    store.setReturnTimeFilter(null)
  }
  open.value = false
}

function onDocMouseDown(e: MouseEvent) {
  if (!open.value) return
  const el = rootEl.value
  if (el && !el.contains(e.target as Node)) {
    open.value = false
  }
}

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape') open.value = false
}

onMounted(() => {
  document.addEventListener('mousedown', onDocMouseDown)
  document.addEventListener('keydown', onKey)
})
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', onDocMouseDown)
  document.removeEventListener('keydown', onKey)
})
</script>

<template>
  <div ref="rootEl" class="relative flex min-w-0 flex-col lg:min-w-[220px]">
    <span
      class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
    >
      {{ fieldLabel }}
    </span>
    <button
      type="button"
      class="flex w-full items-center justify-between gap-3 rounded-lg border border-neutral-200 bg-white px-4 py-3 text-left shadow-sm transition-shadow hover:border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800 dark:hover:border-neutral-500"
      :class="open ? 'shadow-md ring-2 ring-[#5cb87c]/25' : ''"
      :aria-expanded="open"
      aria-haspopup="dialog"
      @click="open = !open"
    >
      <span class="flex min-w-0 items-center gap-3">
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
          class="shrink-0 text-neutral-400"
          aria-hidden="true"
        >
          <circle cx="12" cy="12" r="10" />
          <polyline points="12 6 12 12 16 14" />
        </svg>
        <span class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-100">
          {{ displayText || t('search.fields.timePlaceholder') }}
        </span>
      </span>
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
        class="shrink-0 text-neutral-400 transition-transform"
        :class="open ? 'rotate-180' : ''"
        aria-hidden="true"
      >
        <path d="m6 9 6 6 6-6" />
      </svg>
    </button>

    <div
      v-if="open"
      class="absolute left-0 top-full z-30 mt-2 w-[min(100vw-2rem,320px)] rounded-xl border border-neutral-100 bg-white p-4 shadow-xl dark:border-neutral-700 dark:bg-neutral-900"
      role="dialog"
      :aria-label="fieldLabel"
    >
      <p class="sr-only">{{ t('search.time.hintExclusive') }}</p>

      <div class="mb-4 flex border-b border-neutral-200 dark:border-neutral-700">
        <button
          type="button"
          class="relative flex-1 pb-3 text-center text-sm font-medium transition-colors"
          :class="
            mode === 'departure'
              ? 'text-[#5cb87c]'
              : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200'
          "
          @click="mode = 'departure'"
        >
          {{ t('search.time.tabDeparture') }}
          <span
            v-if="mode === 'departure'"
            class="absolute bottom-0 left-2 right-2 h-0.5 rounded-full bg-[#5cb87c]"
            aria-hidden="true"
          />
        </button>
        <button
          type="button"
          class="relative flex-1 pb-3 text-center text-sm font-medium transition-colors"
          :class="
            mode === 'arrival'
              ? 'text-[#5cb87c]'
              : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200'
          "
          @click="mode = 'arrival'"
        >
          {{ t('search.time.tabArrival') }}
          <span
            v-if="mode === 'arrival'"
            class="absolute bottom-0 left-2 right-2 h-0.5 rounded-full bg-[#5cb87c]"
            aria-hidden="true"
          />
        </button>
      </div>

      <div class="relative">
        <select
          :id="`search-time-select-${leg}`"
          v-model="hhmm"
          class="w-full appearance-none rounded-lg border border-neutral-200 bg-white py-3 pl-4 pr-10 text-lg font-medium text-neutral-800 shadow-sm focus:border-[#5cb87c] focus:outline-none focus:ring-1 focus:ring-[#5cb87c] dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
        >
          <option v-for="slot in timeSlots" :key="slot" :value="slot">
            {{ slot }}
          </option>
        </select>
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
          class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400"
          aria-hidden="true"
        >
          <path d="m6 9 6 6 6-6" />
        </svg>
      </div>

      <button
        type="button"
        class="mt-4 w-full rounded-full bg-[#5cb87c] py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#4daa6d]"
        @click="save"
      >
        {{ t('search.time.apply') }}
      </button>

      <button
        type="button"
        class="mt-3 w-full text-center text-sm font-medium text-neutral-500 transition-colors hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200"
        @click="clearFilter"
      >
        {{ t('search.time.clear') }}
      </button>
    </div>
  </div>
</template>
