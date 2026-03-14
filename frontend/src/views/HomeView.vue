<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '@/services/api'

const health = ref<string>('')
const loading = ref(true)

onMounted(async () => {
  try {
    const data = await api.health()
    health.value = data?.message ?? JSON.stringify(data)
  } catch {
    health.value = 'API no disponible (gateway o servicios no levantados)'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Route Searcher</h1>
    <p class="text-gray-600 mb-6">
      Búsqueda de rutas corporativas. Esta es una página de ejemplo que llama al API a través del gateway.
    </p>
    <div class="bg-white rounded-lg shadow p-4">
      <h2 class="text-sm font-semibold text-gray-700 mb-2">Estado del API (placeholder)</h2>
      <p v-if="loading" class="text-gray-500">Cargando…</p>
      <p v-else class="text-sm font-mono text-gray-800">{{ health }}</p>
    </div>
  </div>
</template>
