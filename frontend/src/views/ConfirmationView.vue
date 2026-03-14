<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

const router = useRouter()
const store = useBookingStore()

const booking = computed(() => store.summary)

function goHome() {
  router.push({ name: 'search' })
}

function downloadPdf() {
  // Maqueta
}

function addToCalendar() {
  // Maqueta
}

function addToWallet() {
  // Maqueta
}
</script>

<template>
  <div class="min-h-screen bg-[var(--color-background)]">
    <!-- Mobile header (solo móvil, como referencia) -->
    <header class="md:hidden bg-neutral-800 text-white px-4 py-3 flex items-center gap-3">
      <button type="button" class="p-1" @click="goHome" aria-label="Volver">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m15 18-6-6 6-6" />
        </svg>
      </button>
      <h1 class="text-lg font-medium text-emerald-400 flex-1 text-center pr-7">Confirmation</h1>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-6 md:py-12">
      <!-- Success icon & title -->
      <div class="flex flex-col items-center mb-6 md:mb-10">
        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-emerald-500/15 flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 md:w-10 md:h-10 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12" />
          </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-neutral-900 text-center">Booking Confirmed!</h1>
        <p class="text-neutral-500 mt-2 text-center">Your seat is secured. Have a safe trip!</p>
      </div>

      <div v-if="booking" class="grid md:grid-cols-2 gap-6 md:gap-8">
        <!-- Ticket card -->
        <article class="overflow-hidden rounded-xl shadow-lg bg-white">
          <!-- Green header -->
          <div class="bg-emerald-500 text-white px-5 py-4 flex justify-between items-start">
            <div>
              <p class="text-xs uppercase tracking-wider opacity-80">Pass Status</p>
              <div class="flex items-center gap-2 mt-1">
                <span class="w-2 h-2 rounded-full bg-white" />
                <span class="font-semibold">Active Ticket</span>
              </div>
            </div>
            <div class="text-right">
              <p class="text-xs uppercase tracking-wider opacity-80">Employee ID</p>
              <p class="font-semibold mt-1">{{ booking.employeeId }}</p>
            </div>
          </div>

          <!-- QR section -->
          <div class="bg-white px-5 py-6 flex flex-col items-center">
            <div class="bg-emerald-500 p-4 rounded-lg">
              <div class="bg-white p-3 rounded">
                <div class="flex flex-col items-center gap-2">
                  <div class="flex items-center gap-1 text-xs text-neutral-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                      <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>Route</span>
                  </div>
                  <p class="font-semibold text-neutral-900">Booking</p>
                  <div class="grid grid-cols-8 gap-0.5 w-24 h-24">
                    <div v-for="i in 64" :key="i" class="aspect-square rounded-sm" :class="i % 3 === 0 ? 'bg-neutral-900' : 'bg-white'" />
                  </div>
                  <div class="flex gap-1 mt-1">
                    <div v-for="i in 5" :key="i" class="w-1 h-3 bg-neutral-900 rounded-sm" />
                  </div>
                </div>
              </div>
            </div>
            <p class="text-sm text-neutral-500 mt-4">{{ booking.ticketRef }}</p>
          </div>

          <!-- Booking details grid -->
          <div class="bg-white px-5 py-4 border-t border-neutral-200">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs uppercase tracking-wider text-emerald-600">Route ID</p>
                <p class="font-semibold text-neutral-900 mt-1">{{ booking.routeId }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs uppercase tracking-wider text-emerald-600">Seat Number</p>
                <p class="font-semibold text-neutral-900 mt-1">{{ booking.seatNumber }}</p>
              </div>
              <div>
                <p class="text-xs uppercase tracking-wider text-emerald-600">Travel Date</p>
                <p class="font-semibold text-neutral-900 mt-1">{{ booking.travelDate }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs uppercase tracking-wider text-emerald-600">Passenger</p>
                <p class="font-semibold text-neutral-900 mt-1">{{ booking.passenger }}</p>
              </div>
            </div>
          </div>

          <!-- Ticket notch effect (dashed separator) -->
          <div class="relative bg-white">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-neutral-50 rounded-r-full" />
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-neutral-50 rounded-l-full" />
            <div class="border-t border-dashed border-neutral-200 mx-4" />
          </div>

          <!-- Trip times -->
          <div class="bg-white px-5 py-4 space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-3 flex-wrap">
                <span class="text-lg font-semibold text-neutral-900">{{ booking.outbound.time }}</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" />
                <span class="text-neutral-500">{{ booking.outbound.from }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0">
                  <path d="M5 12h14" />
                  <path d="m12 5 7 7-7 7" />
                </svg>
                <span class="text-neutral-500">{{ booking.outbound.to }}</span>
              </div>
              <span class="text-xs font-medium text-emerald-600 border border-emerald-500 rounded-full px-3 py-1 shrink-0">OUTBOUND</span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-2">
              <div class="flex items-center gap-3 flex-wrap">
                <span class="text-lg font-semibold text-neutral-900">{{ booking.returnTrip.time }}</span>
                <span class="w-2 h-2 rounded-full border-2 border-neutral-400 shrink-0" />
                <span class="text-neutral-500">{{ booking.returnTrip.from }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-400 shrink-0">
                  <path d="M5 12h14" />
                  <path d="m12 5 7 7-7 7" />
                </svg>
                <span class="text-neutral-500">{{ booking.returnTrip.to }}</span>
              </div>
              <span class="text-xs font-medium text-neutral-500 border border-neutral-200 rounded-full px-3 py-1 shrink-0">RETURN</span>
            </div>
          </div>
        </article>

        <!-- Actions & map column -->
        <div class="space-y-6">
          <div class="grid grid-cols-2 gap-3">
            <button type="button" class="h-12 flex items-center justify-center gap-2 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors text-neutral-700 font-medium" @click="addToCalendar">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
              </svg>
              Calendar
            </button>
            <button type="button" class="h-12 flex items-center justify-center gap-2 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors text-neutral-700 font-medium" @click="downloadPdf">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" x2="12" y1="15" y2="3" />
              </svg>
              PDF Ticket
            </button>
          </div>

          <button type="button" class="w-full h-12 flex items-center justify-center gap-2 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors text-neutral-700 font-medium" @click="addToWallet">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect width="20" height="14" x="2" y="5" rx="2" />
              <path d="M2 10h20" />
            </svg>
            Add to Wallet
          </button>

          <button type="button" class="w-full h-12 bg-neutral-800 text-white font-medium rounded-lg hover:bg-neutral-700 transition-colors" @click="goHome">
            Back to Dashboard
          </button>

          <!-- Route summary map -->
          <div class="space-y-3">
            <h3 class="text-xs uppercase tracking-wider text-emerald-600 font-medium">Route Summary</h3>
            <div class="relative w-full h-48 md:h-64 rounded-xl overflow-hidden bg-neutral-100">
              <div class="absolute inset-0 flex items-center justify-center text-neutral-400 text-sm">Map placeholder</div>
              <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice">
                <path d="M 80 150 Q 150 80 200 100 Q 280 130 320 60" fill="none" stroke="#10b981" stroke-width="3" stroke-dasharray="8 6" stroke-linecap="round" />
                <circle cx="80" cy="150" r="8" fill="#10b981" stroke="white" stroke-width="2" />
                <circle cx="320" cy="60" r="8" fill="#10b981" stroke="white" stroke-width="2" />
              </svg>
            </div>
          </div>

          <p class="text-center text-sm text-neutral-500">
            Booking ID: #{{ booking.bookingId }} •
            <button type="button" class="text-emerald-600 hover:underline inline-flex items-center gap-1">
              Need help?
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
              </svg>
            </button>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
