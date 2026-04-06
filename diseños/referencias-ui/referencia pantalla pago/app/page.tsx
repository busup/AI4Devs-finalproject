export default function PaymentPage() {
  return (
    <div className="min-h-screen bg-neutral-50 pb-32">
      {/* Header */}
      <header className="bg-white border-b border-neutral-200">
        <div className="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between">
            {/* Logo */}
            <div className="flex items-center gap-2">
              <div className="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="white"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M8 6v6" />
                  <path d="M15 6v6" />
                  <path d="M2 12h19.6" />
                  <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                  <circle cx="7" cy="18" r="2" />
                  <path d="M9 18h5" />
                  <circle cx="16" cy="18" r="2" />
                </svg>
              </div>
              <span className="text-xl font-semibold">
                <span className="text-emerald-500">Route</span>
                <span className="text-neutral-800">Searcher</span>
              </span>
            </div>

            {/* User Actions */}
            <div className="flex items-center gap-4">
              <button
                type="button"
                className="p-2 text-neutral-600 hover:text-neutral-800 transition-colors"
                aria-label="Toggle dark mode"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                </svg>
              </button>
              <div
                className="w-9 h-9 rounded-full bg-amber-200"
                aria-label="User avatar"
              />
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {/* Step Indicator */}
        <div className="flex flex-col items-center mb-10">
          <div className="flex items-center gap-2 mb-4">
            <span className="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-semibold">
              4
            </span>
            <span className="text-emerald-500 font-medium">Payment</span>
          </div>
          <h1 className="text-3xl font-bold text-neutral-800 text-center">Complete Your Payment</h1>
          <p className="text-neutral-500 mt-2 text-center">Select your preferred payment method to finalize your booking.</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Left Column - Payment Methods */}
          <div className="lg:col-span-2 space-y-6">
            {/* Payment Methods Section */}
            <section className="bg-white rounded-xl border border-neutral-200 p-6">
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Select Payment Method</h2>
              
              <div className="space-y-3">
                {/* Credit/Debit Card Option */}
                <label className="flex items-center gap-4 p-4 border-2 border-emerald-500 bg-emerald-50 rounded-xl cursor-pointer transition-all">
                  <input
                    type="radio"
                    name="payment"
                    defaultChecked
                    className="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500"
                  />
                  <div className="w-12 h-12 rounded-lg bg-white border border-neutral-200 flex items-center justify-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-neutral-600"
                    >
                      <rect width="20" height="14" x="2" y="5" rx="2" />
                      <line x1="2" x2="22" y1="10" y2="10" />
                    </svg>
                  </div>
                  <div className="flex-1">
                    <p className="text-neutral-800 font-semibold">Credit / Debit Card</p>
                    <p className="text-neutral-500 text-sm">Visa, Mastercard, American Express</p>
                  </div>
                  <div className="flex gap-2">
                    <div className="w-10 h-6 bg-blue-600 rounded flex items-center justify-center">
                      <span className="text-white text-[10px] font-bold">VISA</span>
                    </div>
                    <div className="w-10 h-6 bg-red-500 rounded flex items-center justify-center">
                      <div className="flex">
                        <div className="w-3 h-3 bg-yellow-400 rounded-full -mr-1" />
                        <div className="w-3 h-3 bg-red-600 rounded-full" />
                      </div>
                    </div>
                  </div>
                </label>

                {/* Company Credits Option */}
                <label className="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all">
                  <input
                    type="radio"
                    name="payment"
                    className="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500"
                  />
                  <div className="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-emerald-600"
                    >
                      <circle cx="12" cy="12" r="10" />
                      <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                      <path d="M12 18V6" />
                    </svg>
                  </div>
                  <div className="flex-1">
                    <p className="text-neutral-800 font-semibold">Company Credits</p>
                    <p className="text-neutral-500 text-sm">Available balance: $150.00</p>
                  </div>
                  <span className="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                    150 credits
                  </span>
                </label>

                {/* Corporate Smartpass Option */}
                <label className="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all">
                  <input
                    type="radio"
                    name="payment"
                    className="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500"
                  />
                  <div className="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-amber-600"
                    >
                      <rect width="20" height="14" x="2" y="5" rx="2" />
                      <path d="M2 10h20" />
                    </svg>
                  </div>
                  <div className="flex-1">
                    <p className="text-neutral-800 font-semibold">Corporate Smartpass</p>
                    <p className="text-neutral-500 text-sm">Unlimited Commuting Plan</p>
                  </div>
                  <span className="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                    Active
                  </span>
                </label>

                {/* PayPal Option */}
                <label className="flex items-center gap-4 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-300 transition-all">
                  <input
                    type="radio"
                    name="payment"
                    className="w-5 h-5 text-emerald-500 border-neutral-300 focus:ring-emerald-500"
                  />
                  <div className="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                    <span className="text-blue-600 font-bold text-sm">Pay</span>
                  </div>
                  <div className="flex-1">
                    <p className="text-neutral-800 font-semibold">PayPal</p>
                    <p className="text-neutral-500 text-sm">Pay securely with your PayPal account</p>
                  </div>
                </label>
              </div>
            </section>

            {/* Card Details Form (shown when card is selected) */}
            <section className="bg-white rounded-xl border border-neutral-200 p-6">
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Card Details</h2>
              
              <div className="space-y-4">
                <div>
                  <label htmlFor="cardNumber" className="block text-sm font-medium text-neutral-700 mb-2">
                    Card Number
                  </label>
                  <div className="relative">
                    <input
                      type="text"
                      id="cardNumber"
                      placeholder="1234 5678 9012 3456"
                      className="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    />
                    <div className="absolute right-3 top-1/2 -translate-y-1/2 flex gap-2">
                      <div className="w-8 h-5 bg-blue-600 rounded flex items-center justify-center">
                        <span className="text-white text-[8px] font-bold">VISA</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label htmlFor="expiry" className="block text-sm font-medium text-neutral-700 mb-2">
                      Expiry Date
                    </label>
                    <input
                      type="text"
                      id="expiry"
                      placeholder="MM/YY"
                      className="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    />
                  </div>
                  <div>
                    <label htmlFor="cvv" className="block text-sm font-medium text-neutral-700 mb-2">
                      CVV
                    </label>
                    <input
                      type="text"
                      id="cvv"
                      placeholder="123"
                      className="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    />
                  </div>
                </div>

                <div>
                  <label htmlFor="cardName" className="block text-sm font-medium text-neutral-700 mb-2">
                    Cardholder Name
                  </label>
                  <input
                    type="text"
                    id="cardName"
                    placeholder="John Doe"
                    className="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  />
                </div>

                <label className="flex items-center gap-3 cursor-pointer">
                  <input
                    type="checkbox"
                    className="w-5 h-5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500"
                  />
                  <span className="text-sm text-neutral-600">Save card for future payments</span>
                </label>
              </div>
            </section>
          </div>

          {/* Right Column - Order Summary */}
          <div className="space-y-6">
            {/* Order Summary */}
            <section className="bg-white rounded-xl border border-neutral-200 p-6 sticky top-6">
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Order Summary</h2>
              
              {/* Trip Summary */}
              <div className="pb-4 border-b border-neutral-100 mb-4">
                <div className="flex items-center gap-2 text-sm text-neutral-600 mb-2">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" x2="16" y1="2" y2="6" />
                    <line x1="8" x2="8" y1="2" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                  </svg>
                  <span>Friday, Dec 1, 2023</span>
                </div>
                <p className="text-neutral-800 font-medium">Home - Work Round Trip</p>
              </div>

              {/* Price Breakdown */}
              <div className="space-y-3 mb-4">
                <div className="flex justify-between text-sm">
                  <span className="text-neutral-600">Outbound Journey</span>
                  <span className="text-neutral-800 font-medium">$12.50</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-neutral-600">Return Journey</span>
                  <span className="text-neutral-800 font-medium">$12.50</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-neutral-600">Service Fee</span>
                  <span className="text-neutral-800 font-medium">$1.00</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-emerald-600">Company Discount</span>
                  <span className="text-emerald-600 font-medium">-$6.00</span>
                </div>
              </div>

              {/* Divider */}
              <div className="border-t border-neutral-200 pt-4 mb-4">
                <div className="flex justify-between items-center">
                  <span className="text-neutral-800 font-semibold">Total</span>
                  <span className="text-2xl font-bold text-neutral-800">$20.00</span>
                </div>
              </div>

              {/* Promo Code */}
              <div className="mb-4">
                <div className="flex gap-2">
                  <input
                    type="text"
                    placeholder="Promo code"
                    className="flex-1 px-4 py-2 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                  />
                  <button
                    type="button"
                    className="px-4 py-2 bg-neutral-100 text-neutral-700 font-medium rounded-lg hover:bg-neutral-200 transition-colors text-sm"
                  >
                    Apply
                  </button>
                </div>
              </div>

              {/* Security Badge */}
              <div className="flex items-center gap-2 text-xs text-neutral-500 bg-neutral-50 rounded-lg p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-emerald-500"
                >
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                  <path d="m9 12 2 2 4-4" />
                </svg>
                <span>Your payment is secured with 256-bit SSL encryption</span>
              </div>
            </section>
          </div>
        </div>

        {/* Terms and Conditions Section */}
        <section className="bg-white rounded-xl border border-neutral-200 p-6 mt-6">
          <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-5">Terms & Conditions</h2>
          
          <div className="space-y-4">
            <label className="flex items-start gap-3 cursor-pointer">
              <input
                type="checkbox"
                className="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500"
              />
              <span className="text-sm text-neutral-600">
                I agree to the{" "}
                <a href="#" className="text-emerald-600 hover:underline font-medium">
                  Terms of Service
                </a>{" "}
                and{" "}
                <a href="#" className="text-emerald-600 hover:underline font-medium">
                  Privacy Policy
                </a>{" "}
                of RouteSearcher.
              </span>
            </label>

            <label className="flex items-start gap-3 cursor-pointer">
              <input
                type="checkbox"
                className="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500"
              />
              <span className="text-sm text-neutral-600">
                I understand the{" "}
                <a href="#" className="text-emerald-600 hover:underline font-medium">
                  cancellation policy
                </a>{" "}
                and refund terms. Cancellations made within 2 hours of departure may not be eligible for a full refund.
              </span>
            </label>

            <label className="flex items-start gap-3 cursor-pointer">
              <input
                type="checkbox"
                className="w-5 h-5 mt-0.5 text-emerald-500 border-neutral-300 rounded focus:ring-emerald-500"
              />
              <span className="text-sm text-neutral-600">
                I consent to receive booking confirmations and trip updates via email and SMS.
              </span>
            </label>
          </div>
        </section>
      </main>

      {/* Sticky Footer */}
      <footer className="fixed bottom-0 left-0 right-0 bg-white border-t border-neutral-200 shadow-lg">
        <div className="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            {/* Amount to Pay */}
            <div>
              <p className="text-sm text-neutral-500">Amount to Pay</p>
              <div className="flex items-center gap-3">
                <span className="text-3xl font-bold text-neutral-800">$20.00</span>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex items-center gap-4">
              <button
                type="button"
                className="text-neutral-600 font-medium hover:text-neutral-800 transition-colors"
              >
                Back to Review
              </button>
              <button
                type="button"
                className="inline-flex items-center gap-2 px-8 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                </svg>
                Pay Now
              </button>
            </div>
          </div>
        </div>
      </footer>

      {/* Help Button */}
      <button
        type="button"
        className="fixed bottom-24 right-6 w-12 h-12 bg-neutral-800 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-neutral-700 transition-colors"
        aria-label="Get help"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
        </svg>
      </button>
    </div>
  );
}
