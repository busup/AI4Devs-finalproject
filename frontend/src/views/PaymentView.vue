<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

const router = useRouter()
const store = useBookingStore()

type PaymentMethod = 'card' | 'credits' | 'smartpass' | 'paypal'
const paymentMethod = ref<PaymentMethod>('card')

const summary = ref(store.summary)
const travelDate = summary.value?.travelDate ?? 'Viernes, 1 Dic 2024'

function goBack() {
  router.push({ name: 'review' })
}

function confirmPayment() {
  router.push({ name: 'confirmation' })
}

function goHome() {
  router.push({ name: 'search' })
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50 pb-32">
    <!-- Header (misma referencia que review/pago) -->
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
      <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-2 mb-4">
          <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-semibold">4</span>
          <span class="text-emerald-500 font-medium">Payment</span>
        </div>
        <h1 class="text-3xl font-bold text-neutral-800 text-center">Complete Your Payment</h1>
        <p class="text-neutral-500 mt-2 text-center">Select your preferred payment method to finalize your booking.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column - Payment methods + Card details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Select Payment Method -->
          <section class="bg-white rounded-xl border border-neutral-200 p-6">
            <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Select Payment Method</h2>
            <div class="space-y-3">
              <!-- Credit/Debit Card -->
              <label
                class="flex items-center gap-4 p-4 rounded-xl cursor-pointer transition-all"
                :class="paymentMethod === 'card' ? 'border-2 border-emerald-500 bg-emerald-50' : 'border border-neutral-200 hover:border-neutral-300'"
              >
                <input v-model="paymentMethod" type="radio" value="card" class="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500" />
                <div class="w-12 h-12 rounded-lg bg-white border border-neutral-200 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-neutral-600">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-neutral-800 font-semibold">Credit / Debit Card</p>
                  <p class="text-neutral-500 text-sm">Visa, Mastercard, American Express</p>
                </div>
                <div class="flex gap-2 shrink-0">
                  <div class="w-10 h-6 bg-blue-600 rounded flex items-center justify-center">
                    <span class="text-white text-[10px] font-bold">VISA</span>
                  </div>
                  <div class="w-10 h-6 bg-red-500 rounded flex items-center justify-center">
                    <div class="flex">
                      <div class="w-3 h-3 bg-yellow-400 rounded-full -mr-1" />
                      <div class="w-3 h-3 bg-red-600 rounded-full" />
                    </div>
                  </div>
                </div>
              </label>

              <!-- Company Credits -->
              <label
                class="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all"
                :class="paymentMethod === 'credits' ? 'border-2 border-emerald-500 bg-emerald-50' : ''"
              >
                <input v-model="paymentMethod" type="radio" value="credits" class="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500" />
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                    <path d="M12 18V6" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-neutral-800 font-semibold">Company Credits</p>
                  <p class="text-neutral-500 text-sm">Available balance: $150.00</p>
                </div>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full shrink-0">150 credits</span>
              </label>

              <!-- Corporate Smartpass -->
              <label
                class="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all"
                :class="paymentMethod === 'smartpass' ? 'border-2 border-emerald-500 bg-emerald-50' : ''"
              >
                <input v-model="paymentMethod" type="radio" value="smartpass" class="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500" />
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-600">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <path d="M2 10h20" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-neutral-800 font-semibold">Corporate Smartpass</p>
                  <p class="text-neutral-500 text-sm">Unlimited Commuting Plan</p>
                </div>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full shrink-0">Active</span>
              </label>

              <!-- PayPal -->
              <label
                class="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all"
                :class="paymentMethod === 'paypal' ? 'border-2 border-emerald-500 bg-emerald-50' : ''"
              >
                <input v-model="paymentMethod" type="radio" value="paypal" class="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500" />
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                  <span class="text-blue-600 font-bold text-sm">Pay</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-neutral-800 font-semibold">PayPal</p>
                  <p class="text-neutral-500 text-sm">Pay securely with your PayPal account</p>
                </div>
              </label>
            </div>
          </section>

          <!-- Card Details (visible when card selected) -->
          <section v-if="paymentMethod === 'card'" class="bg-white rounded-xl border border-neutral-200 p-6">
            <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Card Details</h2>
            <div class="space-y-4">
              <div>
                <label for="cardNumber" class="block text-sm font-medium text-neutral-700 mb-2">Card Number</label>
                <div class="relative">
                  <input
                    id="cardNumber"
                    type="text"
                    placeholder="1234 5678 9012 3456"
                    class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  />
                  <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-2">
                    <div class="w-8 h-5 bg-blue-600 rounded flex items-center justify-center">
                      <span class="text-white text-[8px] font-bold">VISA</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="expiry" class="block text-sm font-medium text-neutral-700 mb-2">Expiry Date</label>
                  <input id="expiry" type="text" placeholder="MM/YY" class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                </div>
                <div>
                  <label for="cvv" class="block text-sm font-medium text-neutral-700 mb-2">CVV</label>
                  <input id="cvv" type="text" placeholder="123" class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                </div>
              </div>
              <div>
                <label for="cardName" class="block text-sm font-medium text-neutral-700 mb-2">Cardholder Name</label>
                <input id="cardName" type="text" placeholder="John Doe" class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
              </div>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="w-5 h-5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500" />
                <span class="text-sm text-neutral-600">Save card for future payments</span>
              </label>
            </div>
          </section>
        </div>

        <!-- Right column - Order Summary -->
        <div class="space-y-6">
          <section class="bg-white rounded-xl border border-neutral-200 p-6 lg:sticky lg:top-6">
            <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Order Summary</h2>
            <div class="pb-4 border-b border-neutral-100 mb-4">
              <div class="flex items-center gap-2 text-sm text-neutral-600 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                  <line x1="16" x2="16" y1="2" y2="6" />
                  <line x1="8" x2="8" y1="2" y2="6" />
                  <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <span>{{ travelDate }}</span>
              </div>
              <p class="text-neutral-800 font-medium">Home - Work Round Trip</p>
            </div>
            <div class="space-y-3 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-neutral-600">Outbound Journey</span>
                <span class="text-neutral-800 font-medium">$12.50</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-neutral-600">Return Journey</span>
                <span class="text-neutral-800 font-medium">$12.50</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-neutral-600">Service Fee</span>
                <span class="text-neutral-800 font-medium">$1.00</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-emerald-600">Company Discount</span>
                <span class="text-emerald-600 font-medium">-$6.00</span>
              </div>
            </div>
            <div class="border-t border-neutral-200 pt-4 mb-4">
              <div class="flex justify-between items-center">
                <span class="text-neutral-800 font-semibold">Total</span>
                <span class="text-2xl font-bold text-neutral-800">$20.00</span>
              </div>
            </div>
            <div class="mb-4">
              <div class="flex gap-2">
                <input type="text" placeholder="Promo code" class="flex-1 px-4 py-2 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                <button type="button" class="px-4 py-2 bg-neutral-100 text-neutral-700 font-medium rounded-lg hover:bg-neutral-200 transition-colors text-sm">Apply</button>
              </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-neutral-500 bg-neutral-50 rounded-lg p-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500 shrink-0">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                <path d="m9 12 2 2 4-4" />
              </svg>
              <span>Your payment is secured with 256-bit SSL encryption</span>
            </div>
          </section>
        </div>
      </div>

      <!-- Terms & Conditions -->
      <section class="bg-white rounded-xl border border-neutral-200 p-6 mt-6">
        <h2 class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Terms & Conditions</h2>
        <div class="space-y-4">
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" class="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500" />
            <span class="text-sm text-neutral-600">
              I agree to the
              <a href="#" class="text-emerald-600 hover:underline font-medium">Terms of Service</a>
              and
              <a href="#" class="text-emerald-600 hover:underline font-medium">Privacy Policy</a>
              of RouteSearcher.
            </span>
          </label>
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" class="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500" />
            <span class="text-sm text-neutral-600">
              I understand the
              <a href="#" class="text-emerald-600 hover:underline font-medium">cancellation policy</a>
              and refund terms. Cancellations made within 2 hours of departure may not be eligible for a full refund.
            </span>
          </label>
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" class="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500" />
            <span class="text-sm text-neutral-600">I consent to receive booking confirmations and trip updates via email and SMS.</span>
          </label>
        </div>
      </section>
    </main>

    <!-- Sticky footer -->
    <footer class="fixed bottom-0 left-0 right-0 bg-white border-t border-neutral-200 shadow-lg">
      <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <p class="text-sm text-neutral-500">Amount to Pay</p>
            <div class="flex items-center gap-3">
              <span class="text-3xl font-bold text-neutral-800">$20.00</span>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <button type="button" class="text-neutral-600 font-medium hover:text-neutral-800 transition-colors" @click="goBack">Back to Review</button>
            <button type="button" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-sm" @click="confirmPayment">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
              </svg>
              Pay Now
            </button>
          </div>
        </div>
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
