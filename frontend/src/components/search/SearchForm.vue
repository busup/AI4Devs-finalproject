<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

const router = useRouter()
const store = useBookingStore()

const origin = ref(store.search.origin)
const destination = ref(store.search.destination)
const date = ref(store.search.date || getDefaultDate())
const journeyType = ref<'outbound' | 'return' | 'roundtrip'>(store.search.journeyType)

function getDefaultDate() {
  const d = new Date()
  return d.toISOString().slice(0, 10)
}

const canSubmit = computed(() => origin.value && destination.value && date.value)

function submit() {
  if (!canSubmit.value) return
  store.setSearch({
    origin: origin.value,
    destination: destination.value,
    date: date.value,
    journeyType: journeyType.value,
  })
  router.push({ name: 'review' })
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <div>
      <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Origen</label>
      <input
        id="origin"
        v-model="origin"
        type="text"
        class="w-full rounded-card border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-primary focus:ring-1 focus:ring-primary"
        placeholder="Dirección o parada"
      />
    </div>
    <div>
      <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
      <input
        id="destination"
        v-model="destination"
        type="text"
        class="w-full rounded-card border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-primary focus:ring-1 focus:ring-primary"
        placeholder="Dirección o parada"
      />
    </div>
    <div>
      <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
      <input
        id="date"
        v-model="date"
        type="date"
        class="w-full rounded-card border border-gray-300 px-3 py-2 text-gray-900 focus:border-primary focus:ring-1 focus:ring-primary"
      />
    </div>
    <div>
      <span class="block text-sm font-medium text-gray-700 mb-2">Tipo de viaje</span>
      <div class="flex gap-3 flex-wrap">
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input v-model="journeyType" type="radio" value="outbound" class="text-primary focus:ring-primary" />
          <span class="text-sm text-gray-700">Solo ida</span>
        </label>
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input v-model="journeyType" type="radio" value="return" class="text-primary focus:ring-primary" />
          <span class="text-sm text-gray-700">Solo vuelta</span>
        </label>
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input v-model="journeyType" type="radio" value="roundtrip" class="text-primary focus:ring-primary" />
          <span class="text-sm text-gray-700">Ida y vuelta</span>
        </label>
      </div>
    </div>
    <div class="pt-2">
      <button
        type="submit"
        :disabled="!canSubmit"
        class="w-full sm:w-auto px-6 py-2.5 rounded-card bg-primary text-primary-foreground font-medium hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
      >
        Buscar rutas
      </button>
    </div>
  </form>
</template>
