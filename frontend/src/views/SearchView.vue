<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

const router = useRouter()
const store = useBookingStore()

const journeyType = ref<'roundtrip' | 'oneway'>('roundtrip')
const origin = ref('Home (Current Location)')
const destination = ref('Work - Head Office')
const departureDate = ref('12/01/2023')
const arrivalPref = ref('Arrive before 09:00 AM')
const selectedRouteId = ref('route-ab')

const routes = [
  { id: 'route-ab', name: 'Route AB - Corporate Express', subtitle: 'Home → Head Office', dep: '08:00', arr: '08:45', recommended: true },
  { id: 'route-cb', name: 'Route CB - City Direct', subtitle: 'Home → Head Office', dep: '07:45', arr: '08:35', recommended: false },
  { id: 'route-de', name: 'Route DE - Express West', subtitle: 'Home → Head Office', dep: '08:15', arr: '09:05', recommended: false },
]

function goHome() {
  router.push({ name: 'search' })
}

function swapOriginDestination() {
  const o = origin.value
  origin.value = destination.value
  destination.value = o
}

function selectRoute(id: string) {
  selectedRouteId.value = id
}

function continueToReturn() {
  store.setSearch({
    origin: origin.value,
    destination: destination.value,
    date: departureDate.value,
    journeyType: journeyType.value === 'roundtrip' ? 'roundtrip' : 'outbound',
  })
  router.push({ name: 'review' })
}
</script>

<template>
  <div class="min-h-screen bg-neutral-100">
    <!-- Header -->
    <header class="bg-white border-b border-neutral-200">
      <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <button type="button" class="flex items-center gap-2" @click="goHome">
            <div class="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 6v6" />
                <path d="M15 6v6" />
                <path d="M2 12h19.6" />
                <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                <circle cx="7" cy="18" r="2" />
                <path d="M9 18h5" />
                <circle cx="16" cy="18" r="2" />
              </svg>
            </div>
            <span class="text-xl font-semibold">
              <span class="text-emerald-500">Route</span>
              <span class="text-neutral-800">Searcher</span>
            </span>
          </button>
          <nav class="hidden md:flex items-center gap-8">
            <span class="text-emerald-500 font-medium">Find Routes</span>
            <a href="#" class="text-neutral-600 hover:text-neutral-800 transition-colors">My Bookings</a>
            <a href="#" class="text-neutral-600 hover:text-neutral-800 transition-colors">Company Pass</a>
          </nav>
          <div class="flex items-center gap-4">
            <button type="button" class="p-2 text-neutral-600 hover:text-neutral-800 transition-colors" aria-label="Toggle dark mode">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
              </svg>
            </button>
            <button type="button" class="w-9 h-9 rounded-lg border-2 border-emerald-500 bg-emerald-50 flex items-center justify-center text-emerald-600" aria-label="User menu">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Progress steps -->
    <div class="bg-white border-b border-neutral-200">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <nav aria-label="Progress" class="flex items-center justify-center gap-0">
          <div class="flex flex-col items-center">
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500 text-white text-sm font-semibold">1</span>
            <span class="mt-2 text-sm font-medium text-emerald-500">Outbound</span>
          </div>
          <div class="w-24 lg:w-48 h-0.5 bg-neutral-300 mx-4" />
          <div class="flex flex-col items-center">
            <span class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-neutral-300 text-neutral-400 text-sm font-semibold">2</span>
            <span class="mt-2 text-sm text-neutral-400">Return</span>
          </div>
          <div class="w-24 lg:w-48 h-0.5 bg-neutral-300 mx-4" />
          <div class="flex flex-col items-center">
            <span class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-neutral-300 text-neutral-400 text-sm font-semibold">3</span>
            <span class="mt-2 text-sm text-neutral-400">Review</span>
          </div>
        </nav>
      </div>
    </div>

    <!-- Booking bar -->
    <div class="bg-white border-b border-neutral-200 shadow-sm">
      <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
        <div class="inline-flex items-center rounded-full bg-neutral-100 p-1 mb-5">
          <button type="button" class="px-5 py-2 rounded-full text-sm transition-colors" :class="journeyType === 'roundtrip' ? 'bg-white text-emerald-600 font-medium shadow-sm' : 'text-neutral-600 hover:text-neutral-800'" @click="journeyType = 'roundtrip'">Round-trip</button>
          <button type="button" class="px-5 py-2 rounded-full text-sm transition-colors" :class="journeyType === 'oneway' ? 'bg-white text-emerald-600 font-medium shadow-sm' : 'text-neutral-600 hover:text-neutral-800'" @click="journeyType = 'oneway'">One-way</button>
        </div>
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
          <div class="flex flex-col flex-1 min-w-0">
            <label class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">Origin</label>
            <button type="button" class="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors text-left bg-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0">
                <circle cx="12" cy="12" r="10" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              <span class="text-neutral-700 truncate">{{ origin }}</span>
            </button>
          </div>
          <div class="hidden lg:flex items-center justify-center pb-1.5">
            <button type="button" class="p-2 text-neutral-400 hover:text-neutral-600 transition-colors" aria-label="Swap" @click="swapOriginDestination">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m16 3 4 4-4 4" />
                <path d="M20 7H4" />
                <path d="m8 21-4-4 4-4" />
                <path d="M4 17h16" />
              </svg>
            </button>
          </div>
          <div class="flex flex-col flex-1 min-w-0">
            <label class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">Destination</label>
            <button type="button" class="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors text-left bg-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500 shrink-0">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <span class="text-neutral-700 truncate">{{ destination }}</span>
            </button>
          </div>
          <div class="hidden lg:block w-px h-10 bg-neutral-200 self-end mb-1.5" />
          <div class="flex flex-col min-w-0">
            <label class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">Departure Date</label>
            <button type="button" class="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors bg-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
              </svg>
              <span class="text-neutral-700">{{ departureDate }}</span>
            </button>
          </div>
          <div class="flex flex-col min-w-0">
            <label class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">Arrival Time Pref.</label>
            <button type="button" class="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors bg-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <span class="text-neutral-700 whitespace-nowrap">{{ arrivalPref }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0 ml-2">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Route list -->
        <div class="lg:col-span-1">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-neutral-800">Select Outbound Route</h2>
            <div class="flex items-center gap-4">
              <span class="text-sm text-neutral-500"><span class="font-semibold text-neutral-700">4</span> available</span>
              <button type="button" class="flex items-center gap-1 text-emerald-500 text-sm font-medium hover:text-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                Morning
              </button>
            </div>
          </div>
          <div class="flex flex-col gap-4">
            <article
              v-for="route in routes"
              :key="route.id"
              class="bg-white rounded-xl shadow-sm p-5 relative cursor-pointer transition-colors"
              :class="selectedRouteId === route.id ? 'border-2 border-emerald-500' : 'border border-neutral-200 hover:border-neutral-300'"
              @click="selectRoute(route.id)"
            >
              <span v-if="route.recommended" class="absolute top-0 right-4 -translate-y-1/2 px-3 py-1 bg-emerald-500 text-white text-xs font-semibold uppercase rounded-full">Recommended</span>
              <h3 class="text-neutral-800 font-semibold text-lg">{{ route.name }}</h3>
              <p class="text-neutral-500 text-sm mb-3">{{ route.subtitle }}</p>
              <div class="flex items-center gap-4 mb-4">
                <div class="flex flex-col">
                  <span class="text-2xl font-bold" :class="selectedRouteId === route.id ? 'text-neutral-800' : 'text-neutral-400'">{{ route.dep }}</span>
                  <span class="text-xs text-neutral-500 uppercase">Departure</span>
                </div>
                <div class="flex-1 flex items-center gap-2">
                  <div class="flex-1 border-t-2 border-dashed border-neutral-300" />
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400">
                    <path d="M8 6v6" />
                    <path d="M15 6v6" />
                    <path d="M2 12h19.6" />
                    <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                    <circle cx="7" cy="18" r="2" />
                    <path d="M9 18h5" />
                    <circle cx="16" cy="18" r="2" />
                  </svg>
                  <div class="flex-1 border-t-2 border-dashed border-neutral-300" />
                </div>
                <div class="flex flex-col text-right">
                  <span class="text-2xl font-bold text-neutral-400">{{ route.arr }}</span>
                  <span class="text-xs text-neutral-500 uppercase">Arrival</span>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400">
                    <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                    <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                    <line x1="12" x2="12.01" y1="20" y2="20" />
                  </svg>
                  <svg v-if="selectedRouteId === route.id" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                  </svg>
                </div>
                <div v-if="selectedRouteId === route.id" class="flex items-center gap-2 text-emerald-500 font-semibold text-sm">
                  SELECTED
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                  </svg>
                </div>
                <button v-else type="button" class="text-neutral-600 font-semibold text-sm uppercase hover:text-neutral-800 transition-colors" @click.stop="selectRoute(route.id)">Select Route</button>
              </div>
            </article>
          </div>
        </div>

        <!-- Map panel -->
        <div class="lg:col-span-2">
          <div class="bg-emerald-900/90 rounded-xl h-[500px] lg:h-[600px] relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
              <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5" />
                  </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
              </svg>
            </div>
            <div class="absolute top-4 left-4 right-4">
              <div class="bg-white rounded-lg shadow-lg flex items-center px-4 py-3 max-w-md">
                <button type="button" class="text-neutral-400 hover:text-neutral-600 mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" x2="20" y1="12" y2="12" />
                    <line x1="4" x2="20" y1="6" y2="6" />
                    <line x1="4" x2="20" y1="18" y2="18" />
                  </svg>
                </button>
                <input type="text" placeholder="Search Google Maps" class="flex-1 text-neutral-600 placeholder-neutral-400 outline-none bg-transparent" />
                <button type="button" class="text-emerald-500 hover:text-emerald-600 ml-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="3 11 22 2 13 21 11 13 3 11" />
                  </svg>
                </button>
              </div>
            </div>
            <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice">
              <path d="M 50 250 Q 100 200 150 180 Q 200 160 250 120 Q 300 80 350 50" stroke="#3b82f6" stroke-width="4" fill="none" stroke-linecap="round" />
              <circle cx="50" cy="250" r="8" fill="#3b82f6" stroke="white" stroke-width="2" />
              <circle cx="350" cy="50" r="6" fill="#22c55e" stroke="white" stroke-width="2" />
            </svg>
            <div class="absolute bottom-4 left-4 right-4">
              <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600">
                      <path d="m5 12 7-7 7 7" />
                      <path d="M12 19V5" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-neutral-500 uppercase tracking-wide">Estimated Duration</p>
                    <p class="text-lg font-bold text-neutral-800">45 mins <span class="font-normal text-neutral-500">via Route AB</span></p>
                  </div>
                </div>
                <button type="button" class="px-6 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors whitespace-nowrap" @click="continueToReturn">Continue to Return</button>
              </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex flex-col gap-2">
              <button type="button" class="w-10 h-10 bg-white rounded-lg shadow-lg flex items-center justify-center text-neutral-600 hover:text-neutral-800 transition-colors" aria-label="Zoom in">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" x2="12" y1="5" y2="19" />
                  <line x1="5" x2="19" y1="12" y2="12" />
                </svg>
              </button>
              <button type="button" class="w-10 h-10 bg-white rounded-lg shadow-lg flex items-center justify-center text-neutral-600 hover:text-neutral-800 transition-colors" aria-label="Zoom out">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="5" x2="19" y1="12" y2="12" />
                </svg>
              </button>
            </div>
            <div class="absolute bottom-24 left-4">
              <button type="button" class="bg-neutral-800/80 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-2 hover:bg-neutral-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                  <line x1="3" x2="21" y1="9" y2="9" />
                  <line x1="3" x2="21" y1="15" y2="15" />
                  <line x1="9" x2="9" y1="3" y2="21" />
                  <line x1="15" x2="15" y1="3" y2="21" />
                </svg>
                Satellite
              </button>
            </div>
            <div class="absolute bottom-24 right-4">
              <button type="button" class="w-10 h-10 bg-neutral-800 rounded-full shadow-lg flex items-center justify-center text-white hover:bg-neutral-700 transition-colors" aria-label="Support">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 18v-6a9 9 0 0 1 18 0v6" />
                  <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
