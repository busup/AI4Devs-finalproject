export default function ReviewPage() {
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
        <div className="flex flex-col items-center mb-8">
          <div className="flex items-center gap-2 mb-4">
            <span className="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-semibold">
              3
            </span>
            <span className="text-emerald-500 font-medium">Final Verification</span>
          </div>
          <h1 className="text-3xl font-bold text-neutral-800 text-center">Review Your Booking</h1>
          <p className="text-neutral-500 mt-2 text-center">Please verify your journey details before final confirmation.</p>
        </div>

        {/* Journey Cards - Side by Side */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          {/* Outbound Journey Card */}
          <article className="bg-white rounded-xl border border-neutral-200 p-6">
            <div className="flex items-center gap-2 mb-5">
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
                className="text-neutral-400"
              >
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider">Outbound Journey</h2>
            </div>

            <div className="mb-5">
              <p className="text-xs text-neutral-400 mb-1">Date</p>
              <p className="text-neutral-800 font-semibold">Friday, Dec 1, 2023</p>
            </div>

            <div className="flex items-center justify-between mb-5">
              <div>
                <p className="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Departure</p>
                <p className="text-2xl font-bold text-neutral-800">08:00 AM</p>
              </div>
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
                className="text-neutral-300"
              >
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <div className="text-right">
                <p className="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Arrival</p>
                <p className="text-2xl font-bold text-neutral-800">08:45 AM</p>
              </div>
            </div>

            <div className="mb-5">
              <p className="text-xs text-neutral-400 mb-1">Route</p>
              <p className="text-neutral-800 font-medium">Route AB - Corporate Express</p>
            </div>

            <div className="bg-neutral-50 rounded-lg p-4 flex items-start gap-3">
              <div className="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
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
                  className="text-emerald-600"
                >
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              <div>
                <p className="text-neutral-800 font-medium text-sm">Oak Street Corner</p>
                <p className="text-neutral-500 text-xs flex items-center gap-1 mt-0.5">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                  </svg>
                  2 min walk from your location
                </p>
              </div>
            </div>
          </article>

          {/* Return Journey Card */}
          <article className="bg-white rounded-xl border border-neutral-200 p-6">
            <div className="flex items-center gap-2 mb-5">
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
                className="text-neutral-400"
              >
                <path d="M19 12H5" />
                <path d="m12 19-7-7 7-7" />
              </svg>
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider">Return Journey</h2>
            </div>

            <div className="mb-5">
              <p className="text-xs text-neutral-400 mb-1">Date</p>
              <p className="text-neutral-800 font-semibold">Friday, Dec 1, 2023</p>
            </div>

            <div className="flex items-center justify-between mb-5">
              <div>
                <p className="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Departure</p>
                <p className="text-2xl font-bold text-neutral-800">05:30 PM</p>
              </div>
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
                className="text-neutral-300"
              >
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
              </svg>
              <div className="text-right">
                <p className="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-1">Arrival</p>
                <p className="text-2xl font-bold text-neutral-800">06:15 PM</p>
              </div>
            </div>

            <div className="mb-5">
              <p className="text-xs text-neutral-400 mb-1">Route</p>
              <p className="text-neutral-800 font-medium">Route BA - Evening Direct</p>
            </div>

            <div className="bg-neutral-50 rounded-lg p-4 flex items-start gap-3">
              <div className="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
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
                  className="text-emerald-600"
                >
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
                <p className="text-neutral-800 font-medium text-sm">Main Office Gate B</p>
                <p className="text-neutral-500 text-xs flex items-center gap-1 mt-0.5">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                  </svg>
                  1 min walk from desk
                </p>
              </div>
            </div>
          </article>
        </div>

        {/* Passenger & Payment Info */}
        <div className="bg-white rounded-xl border border-neutral-200 p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Passenger Information */}
            <div>
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-4">Passenger Information</h2>
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full bg-amber-200" />
                <div>
                  <p className="text-neutral-800 font-semibold">Alex Thompson</p>
                  <p className="text-neutral-500 text-sm">Employee ID: #7729-RS</p>
                </div>
              </div>
            </div>

            {/* Payment Method */}
            <div className="relative">
              <h2 className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-4">Payment Method</h2>
              <div className="flex items-center gap-4">
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
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                  </svg>
                </div>
                <div>
                  <p className="text-neutral-800 font-semibold uppercase text-sm">Corporate Smartpass</p>
                  <p className="text-neutral-500 text-sm">Unlimited Commuting Plan</p>
                </div>
              </div>
              {/* Clipboard icon */}
              <div className="absolute top-0 right-0">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="40"
                  height="40"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-neutral-200"
                >
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
      </main>

      {/* Sticky Footer */}
      <footer className="fixed bottom-0 left-0 right-0 bg-white border-t border-neutral-200 shadow-lg">
        <div className="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            {/* Grand Total */}
            <div>
              <p className="text-sm text-neutral-500">Grand Total</p>
              <div className="flex items-center gap-3">
                <span className="text-3xl font-bold text-neutral-800">$0.00</span>
                <span className="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                  Fully Covered by Company
                </span>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex items-center gap-4">
              <button
                type="button"
                className="text-neutral-600 font-medium hover:text-neutral-800 transition-colors"
              >
                Go Back & Edit
              </button>
              <button
                type="button"
                className="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors shadow-sm"
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
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                Confirm & Book Seat
              </button>
            </div>
          </div>

          {/* Disclaimer */}
          <p className="text-center text-xs text-neutral-400 mt-3">
            {"By clicking Confirm, you agree to the company's commuting policy and data privacy guidelines."}
          </p>
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
