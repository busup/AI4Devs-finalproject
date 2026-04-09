<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'

import { useSearchStore } from '@/stores/search'

const { t } = useI18n()
const store = useSearchStore()
const { departureDate, departureDateDisplay } = storeToRefs(store)

const open = ref(false)
const monthYearOpen = ref(false)
const rootEl = ref<HTMLElement | null>(null)

/** Fecha seleccionada en el calendario antes de confirmar con Aplicar */
const pendingDate = ref(departureDate.value)

/** Mes visible en el calendario (día 1 de ese mes) */
const viewMonth = ref(new Date())

function toISO(d: Date): string {
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

function buildWeeks(anchor: Date): Date[][] {
  const y = anchor.getFullYear()
  const m = anchor.getMonth()
  const first = new Date(y, m, 1)
  const mondayOffset = (first.getDay() + 6) % 7
  const start = new Date(y, m, 1 - mondayOffset)
  const weeks: Date[][] = []
  for (let w = 0; w < 6; w++) {
    const row: Date[] = []
    for (let d = 0; d < 7; d++) {
      const cell = new Date(start)
      cell.setDate(start.getDate() + w * 7 + d)
      row.push(cell)
    }
    weeks.push(row)
  }
  return weeks
}

const weeks = computed(() => buildWeeks(viewMonth.value))

const monthYearLabel = computed(() => {
  const d = viewMonth.value
  const raw = new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(d)
  return raw.charAt(0).toUpperCase() + raw.slice(1)
})

const viewMonthMonth = computed({
  get: () => viewMonth.value.getMonth(),
  set: (v: number) => {
    const d = new Date(viewMonth.value)
    d.setMonth(v)
    viewMonth.value = d
  },
})

const viewMonthYear = computed({
  get: () => viewMonth.value.getFullYear(),
  set: (v: number) => {
    const d = new Date(viewMonth.value)
    d.setFullYear(v)
    viewMonth.value = d
  },
})

const yearOptions = computed(() => {
  const y = new Date().getFullYear()
  return Array.from({ length: 12 }, (_, i) => y - 2 + i)
})

function monthLabel(m: number): string {
  return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(new Date(2000, m, 1))
}

const weekdayLabels = computed(() =>
  t('search.date.weekdaysShort')
    .split(',')
    .map((s) => s.trim()),
)

function isWeekend(d: Date): boolean {
  const day = d.getDay()
  return day === 0 || day === 6
}

function sameMonthInView(d: Date): boolean {
  return d.getMonth() === viewMonth.value.getMonth() && d.getFullYear() === viewMonth.value.getFullYear()
}

function isSelected(d: Date): boolean {
  return toISO(d) === pendingDate.value
}

watch(open, (v) => {
  if (v) {
    pendingDate.value = departureDate.value
    monthYearOpen.value = false
    const [y, mo] = departureDate.value.split('-').map(Number)
    viewMonth.value = new Date(y, mo - 1, 1)
  }
})

function applyDate() {
  departureDate.value = pendingDate.value
  open.value = false
}

function pickDay(d: Date) {
  pendingDate.value = toISO(d)
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
      {{ t('search.fields.departureDate') }}
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
          <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
          <line x1="16" x2="16" y1="2" y2="6" />
          <line x1="8" x2="8" y1="2" y2="6" />
          <line x1="3" x2="21" y1="10" y2="10" />
        </svg>
        <span class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-100">{{
          departureDateDisplay
        }}</span>
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
      :aria-label="t('search.date.dialogAria')"
    >
      <div class="mb-4 flex flex-col items-center gap-2">
        <div class="flex items-center justify-center gap-1">
          <span class="text-base font-bold text-neutral-800 dark:text-neutral-100">{{ monthYearLabel }}</span>
          <button
            type="button"
            class="rounded-md p-1 text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
            :aria-expanded="monthYearOpen"
            :aria-label="t('search.date.pickMonthYear')"
            @click.stop="monthYearOpen = !monthYearOpen"
          >
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
              <path d="m6 9 6 6 6-6" />
            </svg>
          </button>
        </div>

        <div v-if="monthYearOpen" class="flex w-full flex-wrap items-center justify-center gap-2">
          <select
            v-model.number="viewMonthMonth"
            class="min-w-0 flex-1 rounded-md border border-neutral-200 bg-white px-2 py-1.5 text-sm text-neutral-800 shadow-sm focus:border-[#5cb87c] focus:outline-none focus:ring-1 focus:ring-[#5cb87c] dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
          >
            <option v-for="mi in 12" :key="mi" :value="mi - 1">
              {{ monthLabel(mi - 1) }}
            </option>
          </select>
          <select
            v-model.number="viewMonthYear"
            class="min-w-0 flex-1 rounded-md border border-neutral-200 bg-white px-2 py-1.5 text-sm text-neutral-800 shadow-sm focus:border-[#5cb87c] focus:outline-none focus:ring-1 focus:ring-[#5cb87c] dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
          >
            <option v-for="yy in yearOptions" :key="yy" :value="yy">
              {{ yy }}
            </option>
          </select>
        </div>
      </div>

      <div
        class="mb-2 grid grid-cols-7 gap-y-1 text-center text-xs font-medium text-neutral-400"
        aria-hidden="true"
      >
        <span v-for="(wd, i) in weekdayLabels" :key="i" class="py-1">{{ wd }}</span>
      </div>

      <div class="grid grid-cols-7 gap-y-1 text-center text-sm">
        <template v-for="(week, wi) in weeks" :key="wi">
          <div v-for="(day, di) in week" :key="`${wi}-${di}`" class="flex h-9 items-center justify-center p-0.5">
            <button
              type="button"
              class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#5cb87c]"
              :class="[
                isSelected(day)
                  ? 'bg-[#5cb87c] text-white shadow-sm'
                  : isWeekend(day) && sameMonthInView(day)
                    ? 'text-neutral-300 dark:text-neutral-600'
                    : sameMonthInView(day)
                      ? 'text-neutral-800 hover:bg-neutral-100 dark:text-neutral-100 dark:hover:bg-neutral-800'
                      : 'text-neutral-300 dark:text-neutral-600'
              ]"
              @click="pickDay(day)"
            >
              {{ day.getDate() }}
            </button>
          </div>
        </template>
      </div>

      <button
        type="button"
        class="mt-4 w-full rounded-full bg-[#5cb87c] py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#4daa6d]"
        @click="applyDate"
      >
        {{ t('search.time.apply') }}
      </button>
    </div>
  </div>
</template>
