<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import { useSearchStore } from '@/stores/search'

defineProps<{
  modelValue: string
  selectId?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const { t } = useI18n()
const store = useSearchStore()
const { terminalStops, terminalStopsLoading } = storeToRefs(store)

const options = computed(() => terminalStops.value)
</script>

<template>
  <div class="relative w-full">
    <select
      :id="selectId"
      :value="modelValue"
      :disabled="terminalStopsLoading || options.length === 0"
      class="field-location box-border h-12 w-full cursor-pointer appearance-none rounded-lg border border-neutral-200 bg-white py-0 pl-4 pr-10 text-left text-base text-neutral-700 outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:bg-neutral-50 disabled:text-neutral-400 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-emerald-500 dark:focus:ring-emerald-600 dark:disabled:bg-neutral-900 dark:disabled:text-neutral-500"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option v-if="terminalStopsLoading" value="" disabled>
        {{ t('search.fields.stopsLoading') }}
      </option>
      <option v-else-if="options.length === 0" value="" disabled>
        {{ t('search.fields.stopsEmpty') }}
      </option>
      <option v-for="s in options" :key="s.stopId" :value="s.stopId">
        {{ s.label }}
      </option>
    </select>
    <span
      class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400"
      aria-hidden="true"
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
      >
        <path d="m6 9 6 6 6-6" />
      </svg>
    </span>
  </div>
</template>
