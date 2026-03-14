<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

const router = useRouter()
const store = useBookingStore()

const summary = computed(() => store.summary)
const search = computed(() => store.search)

function goBack() {
  router.push({ name: 'search' })
}

function goToPayment() {
  router.push({ name: 'payment' })
}

function goHome() {
  router.push({ name: 'search' })
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50 pb-32">
    <!-- Header (referencia review bookings) -->
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
          <div class="flex items-center gap-4">
            <button type="button" class="p-2 text-neutral-600 hover:text-neutral-800 transition-colors" aria-label="Toggle dark mode">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
              </svg>
            </button>
            <div class="w-9 h-9 rounded-full bg-amber-200" aria-hidden="true" />
          </div>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
      <!-- Step indicator -->
      <div class="flex flex-col items-center mb-8">
        <div class="flex items-center gap-2 mb-4">
          <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-semibold">3</span>
          <span class="text-emerald-500 font-medium">Final Verification</span>
        </div>
        <h1 class="text-3xl font-bold text-neutral-800 text-center">Review Your Booking</h1>
        <p class="text-neutral-500 mt-2 text-center">Please verify your journey details before final confirmation.</p>
      </div>

      <div v-if="summary" class="space-y-6">
        <!-- Journey cards - side by side -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Outbound -->
          <article class="bg-white rounded-xl border border-neutral-200 p-6">
            <div class="flex items-center gap-2 mb-5">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-400">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider">Outbound Journey</h2>
            </div>
            <div class="mb-5">
              <p class="text-xs text-neutral-400 mb-1">Date</p>
              <p class="text-neutral-800 font-semibold">{{ summary.travelDate }}</p>
            </div>
            <div class="flex items-center justify-between mb-5">
              <div>
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Departure</p>
                <p class="text-2xl font-bold text-neutral-800">{{ summary.outbound.time }} AM</p>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-300">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <div class="text-right">
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Arrival</p>
                <p class="text-2xl font-bold text-neutral-800">{{ summary.outbound.arrivalTime || '08:45' }} AM</p>
              </div>
            </div>
            <div class="mb-5">
              <p class="text-xs text-neutral-400 mb-1">Route</p>
              <p class="text-neutral-800 font-medium">{{ summary.outbound.routeLabel || summary.routeId }}</p>
            </div>
            <div class="bg-neutral-50 rounded-lg p-4 flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              <div>
                <p class="text-neutral-800 font-medium text-sm">{{ summary.outbound.stopName || summary.outbound.from }}</p>
                <p class="text-neutral-500 text-xs flex items-center gap-1 mt-0.5">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="5" cy="17" r="3" />
                    <path d="M8 17h8" />
                    <circle cx="19" cy="17" r="3" />
                    <path d="M12 12v5" />
                    <path d="M8 5h8" />
                    <path d="M5 5v7" />
                    <path d="M19 5v7" />
                  </svg>
                  {{ summary.outbound.stopWalk || 'Pick-up point' }}
                </p>
              </div>
            </div>
          </article>

          <!-- Return (solo si ida y vuelta) -->
          <article v-if="search.journeyType === 'roundtrip'" class="bg-white rounded-xl border border-neutral-200 p-6">
            <div class="flex items-center gap-2 mb-5">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400">
                <path d="M19 12H5" />
                <path d="m12 19-7-7 7-7" />
              </svg>
              <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider">Return Journey</h2>
            </div>
            <div class="mb-5">
              <p class="text-xs text-neutral-400 mb-1">Date</p>
              <p class="text-neutral-800 font-semibold">{{ summary.travelDate }}</p>
            </div>
            <div class="flex items-center justify-between mb-5">
              <div>
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Departure</p>
                <p class="text-2xl font-bold text-neutral-800">{{ summary.returnTrip.time }} PM</p>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-300">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <div class="text-right">
                <p class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Arrival</p>
                <p class="text-2xl font-bold text-neutral-800">{{ summary.returnTrip.arrivalTime || '18:15' }} PM</p>
              </div>
            </div>
            <div class="mb-5">
              <p class="text-xs text-neutral-400 mb-1">Route</p>
              <p class="text-neutral-800 font-medium">{{ summary.returnTrip.routeLabel || summary.routeId }}</p>
            </div>
            <div class="bg-neutral-50 rounded-lg p-4 flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600">
                  <rect width="16" height="16" x="4" y="4" rx="2" />
                  <rect width="6" height="6" x="9" y="9" rx="1" />
                  <path d="M15 2v2" />
                  <path d="M15 20v2" />
                  <path d="M2 15h2" />
                  <path d="M2 9h2" />
                  <path d="M20 15h2" />
                  <path d="M20 9h2" />
                  <path d="M9 2v2" />
                  <path d="M9 20v2" />
                </svg>
              </div>
              <div>
                <p class="text-neutral-800 font-medium text-sm">{{ summary.returnTrip.stopName || summary.returnTrip.from }}</p>
                <p class="text-neutral-500 text-xs flex items-center gap-1 mt-0.5">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="5" cy="17" r="3" />
                    <path d="M8 17h8" />
                    <circle cx="19" cy="17" r="3" />
                    <path d="M12 12v5" />
                    <path d="M8 5h8" />
                    <path d="M5 5v7" />
                    <path d="M19 5v7" />
                  </svg>
                  {{ summary.returnTrip.stopWalk || 'Drop-off point' }}
                </p>
              </div>
            </div>
          </article>

        </div>

        <!-- Passenger & Payment Info -->
        <div class="bg-white rounded-xl border border-neutral-200 p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-4">Passenger Information</h2>
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-200" />
                <div>
                  <p class="text-neutral-800 font-semibold">{{ summary.passenger }}</p>
                  <p class="text-neutral-500 text-sm">Employee ID: {{ summary.employeeId }}</p>
                </div>
              </div>
            </div>
            <div class="relative">
              <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-4">Payment Method</h2>
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                  </svg>
                </div>
                <div>
                  <p class="text-neutral-800 font-semibold uppercase text-sm">{{ summary.paymentLabel || 'Corporate Smartpass' }}</p>
                  <p class="text-neutral-500 text-sm">{{ summary.paymentSublabel || 'Unlimited Commuting Plan' }}</p>
                </div>
              </div>
              <div class="absolute top-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-neutral-200">
                  <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                  <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                  <path d="M12 11h4" />
                  <path d="M12 16h4" />
                  <path d="M8 11h.01" />
                  <path d="M8 16h.01" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Sticky footer -->
    <footer class="fixed bottom-0 left-0 right-0 bg-white border-t border-neutral-200 shadow-lg">
      <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <p class="text-sm text-neutral-500">Grand Total</p>
            <div class="flex items-center gap-3">
              <span class="text-3xl font-bold text-neutral-800">$0.00</span>
              <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Fully Covered by Company</span>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <button type="button" class="text-neutral-600 font-medium hover:text-neutral-800 transition-colors" @click="goBack">Go Back & Edit</button>
            <button type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-sm" @click="goToPayment">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              Confirm & Book Seat
            </button>
          </div>
        </div>
        <p class="text-center text-xs text-neutral-400 mt-3">By clicking Confirm, you agree to the company's commuting policy and data privacy guidelines.</p>
      </div>
    </footer>

    <!-- Help button -->
    <button type="button" class="fixed bottom-24 right-6 w-12 h-12 bg-neutral-800 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-neutral-700 transition-colors" aria-label="Get help">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
      </svg>
    </button>
  </div>
</template>
