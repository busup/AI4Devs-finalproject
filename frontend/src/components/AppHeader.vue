<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

function goHome() {
  router.push({ name: 'search' })
}
</script>

<template>
  <header class="sticky top-0 z-10 bg-card border-b border-gray-200 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
      <button
        type="button"
        class="text-lg font-semibold text-gray-900 hover:text-primary transition-colors"
        @click="goHome"
      >
        Route Searcher
      </button>
      <nav class="flex items-center gap-4 text-sm text-gray-600">
        <span class="hidden sm:inline">Búsqueda y reserva de rutas</span>
      </nav>
    </div>
  </header>
</template>
