<script setup lang="ts">
import { onMounted, watch } from 'vue'

import AppHeader from '@/components/layout/AppHeader.vue'
import SearchBookingBar from '@/components/search/SearchBookingBar.vue'
import SearchMapPanel from '@/components/search/SearchMapPanel.vue'
import RouteListPanel from '@/components/search/RouteListPanel.vue'
import { useRouteSearchStore } from '@/stores/routeSearch'
import { useSearchStore } from '@/stores/search'

const searchStore = useSearchStore()
const routeSearchStore = useRouteSearchStore()

onMounted(() => {
  void searchStore.loadTerminalStops()
  void routeSearchStore.load()
})

watch(
  () => searchStore.departureDate,
  () => {
    routeSearchStore.resetAfterDateChange()
    void routeSearchStore.load()
  },
)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-neutral-100 transition-colors dark:bg-neutral-950">
    <AppHeader />
    <SearchBookingBar />

    <main
      class="mx-auto flex w-full max-w-7xl flex-1 flex-col min-h-0 px-4 py-6 sm:px-6 lg:px-8"
    >
      <div
        class="grid min-h-0 flex-1 grid-cols-1 gap-6 lg:grid-cols-3 lg:grid-rows-1 lg:items-stretch"
      >
        <div
          class="flex min-h-0 flex-col lg:col-span-1 lg:max-h-[calc(100vh-13rem)]"
        >
          <RouteListPanel />
        </div>
        <div
          class="min-h-0 lg:sticky lg:top-6 lg:self-start lg:max-h-[calc(100vh-13rem)]"
        >
          <SearchMapPanel />
        </div>
      </div>
    </main>
  </div>
</template>
