export default function BookingPage() {
  return (
    <div className="min-h-screen bg-neutral-100">
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

            {/* Navigation */}
            <nav className="hidden md:flex items-center gap-8">
              <a
                href="#"
                className="text-emerald-500 font-medium hover:text-emerald-600 transition-colors"
              >
                Find Routes
              </a>
              <a
                href="#"
                className="text-neutral-600 hover:text-neutral-800 transition-colors"
              >
                My Bookings
              </a>
              <a
                href="#"
                className="text-neutral-600 hover:text-neutral-800 transition-colors"
              >
                Company Pass
              </a>
            </nav>

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
              <button
                type="button"
                className="w-9 h-9 rounded-lg border-2 border-emerald-500 bg-emerald-50 flex items-center justify-center text-emerald-600"
                aria-label="User menu"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Progress Steps */}
      <div className="bg-white border-b border-neutral-200">
        <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <nav aria-label="Progress" className="flex items-center justify-center gap-0">
            {/* Step 1 */}
            <div className="flex flex-col items-center">
              <span className="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500 text-white text-sm font-semibold">
                1
              </span>
              <span className="mt-2 text-sm font-medium text-emerald-500">Outbound</span>
            </div>
            
            <div className="w-24 lg:w-48 h-0.5 bg-neutral-300 mx-4" />
            
            {/* Step 2 */}
            <div className="flex flex-col items-center">
              <span className="flex items-center justify-center w-10 h-10 rounded-full border-2 border-neutral-300 text-neutral-400 text-sm font-semibold">
                2
              </span>
              <span className="mt-2 text-sm text-neutral-400">Return</span>
            </div>
            
            <div className="w-24 lg:w-48 h-0.5 bg-neutral-300 mx-4" />
            
            {/* Step 3 */}
            <div className="flex flex-col items-center">
              <span className="flex items-center justify-center w-10 h-10 rounded-full border-2 border-neutral-300 text-neutral-400 text-sm font-semibold">
                3
              </span>
              <span className="mt-2 text-sm text-neutral-400">Review</span>
            </div>
          </nav>
        </div>
      </div>

      {/* Booking Bar */}
      <div className="bg-white border-b border-neutral-200 shadow-sm">
        <div className="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
          {/* Trip Type Toggle */}
          <div className="inline-flex items-center rounded-full bg-neutral-100 p-1 mb-5">
            <button
              type="button"
              className="px-5 py-2 bg-white text-emerald-600 font-medium rounded-full shadow-sm text-sm"
            >
              Round-trip
            </button>
            <button
              type="button"
              className="px-5 py-2 text-neutral-600 hover:text-neutral-800 rounded-full transition-colors text-sm"
            >
              One-way
            </button>
          </div>

          {/* Booking Fields Row */}
          <div className="flex flex-col lg:flex-row lg:items-end gap-4">
            {/* Origin Field */}
            <div className="flex flex-col flex-1 min-w-0">
              <label className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">
                Origin
              </label>
              <button
                type="button"
                className="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors text-left bg-white"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-neutral-400 shrink-0"
                >
                  <circle cx="12" cy="12" r="10" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <span className="text-neutral-700 truncate">Home (Current Location)</span>
              </button>
            </div>

            {/* Swap Button */}
            <div className="hidden lg:flex items-center justify-center pb-1.5">
              <button
                type="button"
                className="p-2 text-neutral-400 hover:text-neutral-600 transition-colors"
                aria-label="Swap origin and destination"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="m16 3 4 4-4 4" />
                  <path d="M20 7H4" />
                  <path d="m8 21-4-4 4-4" />
                  <path d="M4 17h16" />
                </svg>
              </button>
            </div>

            {/* Destination Field */}
            <div className="flex flex-col flex-1 min-w-0">
              <label className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">
                Destination
              </label>
              <button
                type="button"
                className="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors text-left bg-white"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-emerald-500 shrink-0"
                >
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <span className="text-neutral-700 truncate">Work - Head Office</span>
              </button>
            </div>

            {/* Vertical Divider */}
            <div className="hidden lg:block w-px h-10 bg-neutral-200 self-end mb-1.5" />

            {/* Departure Date Field */}
            <div className="flex flex-col min-w-0">
              <label className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">
                Departure Date
              </label>
              <button
                type="button"
                className="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors bg-white"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-neutral-400 shrink-0"
                >
                  <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                  <line x1="16" x2="16" y1="2" y2="6" />
                  <line x1="8" x2="8" y1="2" y2="6" />
                  <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <span className="text-neutral-700">12/01/2023</span>
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
                  className="text-neutral-400 shrink-0 ml-2"
                >
                  <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                  <line x1="16" x2="16" y1="2" y2="6" />
                  <line x1="8" x2="8" y1="2" y2="6" />
                  <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
              </button>
            </div>

            {/* Arrival Time Preference Field */}
            <div className="flex flex-col min-w-0">
              <label className="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider mb-2">
                Arrival Time Pref.
              </label>
              <button
                type="button"
                className="flex items-center gap-3 px-4 py-3 border border-neutral-200 rounded-lg hover:border-neutral-300 transition-colors bg-white"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="text-neutral-400 shrink-0"
                >
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <span className="text-neutral-700 whitespace-nowrap">Arrive before 09:00 AM</span>
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
                  className="text-neutral-400 shrink-0 ml-2"
                >
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Route List Panel */}
          <div className="lg:col-span-1">
            {/* Section Header */}
            <div className="flex items-center justify-between mb-4">
              <div>
                <h2 className="text-xl font-semibold text-neutral-800">
                  Select Outbound Route
                </h2>
              </div>
              <div className="flex items-center gap-4">
                <span className="text-sm text-neutral-500">
                  <span className="font-semibold text-neutral-700">4</span> available
                </span>
                <button
                  type="button"
                  className="flex items-center gap-1 text-emerald-500 text-sm font-medium hover:text-emerald-600 transition-colors"
                >
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
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                  </svg>
                  Morning
                </button>
              </div>
            </div>

            {/* Route Cards */}
            <div className="flex flex-col gap-4">
              {/* Route Card 1 - Selected */}
              <article className="bg-white rounded-xl shadow-sm p-5 border-2 border-emerald-500 relative">
                <span className="absolute top-0 right-4 -translate-y-1/2 px-3 py-1 bg-emerald-500 text-white text-xs font-semibold uppercase rounded-full">
                  Recommended
                </span>
                <h3 className="text-neutral-800 font-semibold text-lg">
                  Route AB - Corporate Express
                </h3>
                <p className="text-neutral-500 text-sm mb-3">
                  Home → Head Office
                </p>
                <div className="flex items-center gap-4 mb-4">
                  <div className="flex flex-col">
                    <span className="text-2xl font-bold text-neutral-800">08:00</span>
                    <span className="text-xs text-neutral-500 uppercase">Departure</span>
                  </div>
                  <div className="flex-1 flex items-center gap-2">
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
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
                      <path d="M8 6v6" />
                      <path d="M15 6v6" />
                      <path d="M2 12h19.6" />
                      <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                      <circle cx="7" cy="18" r="2" />
                      <path d="M9 18h5" />
                      <circle cx="16" cy="18" r="2" />
                    </svg>
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
                  </div>
                  <div className="flex flex-col text-right">
                    <span className="text-2xl font-bold text-neutral-400">08:45</span>
                    <span className="text-xs text-neutral-500 uppercase">Arrival</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-neutral-400"
                    >
                      <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                      <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                      <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                      <line x1="12" x2="12.01" y1="20" y2="20" />
                    </svg>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-emerald-500"
                    >
                      <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                  </div>
                  <div className="flex items-center gap-2 text-emerald-500 font-semibold text-sm">
                    SELECTED
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
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
                  </div>
                </div>
              </article>

              {/* Route Card 2 */}
              <article className="bg-white rounded-xl shadow-sm p-5 border border-neutral-200 hover:border-neutral-300 transition-colors">
                <h3 className="text-neutral-800 font-semibold text-lg">
                  Route CB - City Direct
                </h3>
                <p className="text-neutral-500 text-sm mb-3">
                  Home → Head Office
                </p>
                <div className="flex items-center gap-4 mb-4">
                  <div className="flex flex-col">
                    <span className="text-2xl font-bold text-neutral-400">07:45</span>
                    <span className="text-xs text-neutral-500 uppercase">Departure</span>
                  </div>
                  <div className="flex-1 flex items-center gap-2">
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
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
                      <path d="M8 6v6" />
                      <path d="M15 6v6" />
                      <path d="M2 12h19.6" />
                      <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                      <circle cx="7" cy="18" r="2" />
                      <path d="M9 18h5" />
                      <circle cx="16" cy="18" r="2" />
                    </svg>
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
                  </div>
                  <div className="flex flex-col text-right">
                    <span className="text-2xl font-bold text-neutral-400">08:35</span>
                    <span className="text-xs text-neutral-500 uppercase">Arrival</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-neutral-400"
                    >
                      <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                      <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                      <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                      <line x1="12" x2="12.01" y1="20" y2="20" />
                    </svg>
                  </div>
                  <button
                    type="button"
                    className="text-neutral-600 font-semibold text-sm uppercase hover:text-neutral-800 transition-colors"
                  >
                    Select Route
                  </button>
                </div>
              </article>

              {/* Route Card 3 */}
              <article className="bg-white rounded-xl shadow-sm p-5 border border-neutral-200 hover:border-neutral-300 transition-colors">
                <h3 className="text-neutral-800 font-semibold text-lg">
                  Route DE - Express West
                </h3>
                <p className="text-neutral-500 text-sm mb-3">
                  Home → Head Office
                </p>
                <div className="flex items-center gap-4 mb-4">
                  <div className="flex flex-col">
                    <span className="text-2xl font-bold text-neutral-400">08:15</span>
                    <span className="text-xs text-neutral-500 uppercase">Departure</span>
                  </div>
                  <div className="flex-1 flex items-center gap-2">
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
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
                      <path d="M8 6v6" />
                      <path d="M15 6v6" />
                      <path d="M2 12h19.6" />
                      <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                      <circle cx="7" cy="18" r="2" />
                      <path d="M9 18h5" />
                      <circle cx="16" cy="18" r="2" />
                    </svg>
                    <div className="flex-1 border-t-2 border-dashed border-neutral-300" />
                  </div>
                  <div className="flex flex-col text-right">
                    <span className="text-2xl font-bold text-neutral-400">09:05</span>
                    <span className="text-xs text-neutral-500 uppercase">Arrival</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      className="text-neutral-400"
                    >
                      <path d="M5 12.55a11 11 0 0 1 14.08 0" />
                      <path d="M1.42 9a16 16 0 0 1 21.16 0" />
                      <path d="M8.53 16.11a6 6 0 0 1 6.95 0" />
                      <line x1="12" x2="12.01" y1="20" y2="20" />
                    </svg>
                  </div>
                  <button
                    type="button"
                    className="text-neutral-600 font-semibold text-sm uppercase hover:text-neutral-800 transition-colors"
                  >
                    Select Route
                  </button>
                </div>
              </article>
            </div>
          </div>

          {/* Map Panel */}
          <div className="lg:col-span-2">
            <div className="bg-emerald-900/90 rounded-xl h-[500px] lg:h-[600px] relative overflow-hidden">
              {/* Map Background Pattern */}
              <div className="absolute inset-0 opacity-20">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                  <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                      <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" strokeWidth="0.5" />
                    </pattern>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
              </div>

              {/* Map Search Bar */}
              <div className="absolute top-4 left-4 right-4">
                <div className="bg-white rounded-lg shadow-lg flex items-center px-4 py-3 max-w-md">
                  <button type="button" className="text-neutral-400 hover:text-neutral-600 mr-3">
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
                      <line x1="4" x2="20" y1="12" y2="12" />
                      <line x1="4" x2="20" y1="6" y2="6" />
                      <line x1="4" x2="20" y1="18" y2="18" />
                    </svg>
                  </button>
                  <input
                    type="text"
                    placeholder="Search Google Maps"
                    className="flex-1 text-neutral-600 placeholder-neutral-400 outline-none"
                  />
                  <button type="button" className="text-neutral-400 hover:text-neutral-600 ml-3">
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
                      <circle cx="11" cy="11" r="8" />
                      <path d="m21 21-4.3-4.3" />
                    </svg>
                  </button>
                  <button type="button" className="text-emerald-500 hover:text-emerald-600 ml-3">
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
                      <polygon points="3 11 22 2 13 21 11 13 3 11" />
                    </svg>
                  </button>
                </div>
              </div>

              {/* Route Line Visualization */}
              <svg className="absolute inset-0 w-full h-full" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice">
                <path
                  d="M 50 250 Q 100 200 150 180 Q 200 160 250 120 Q 300 80 350 50"
                  stroke="#3b82f6"
                  strokeWidth="4"
                  fill="none"
                  strokeLinecap="round"
                />
                {/* Home marker */}
                <circle cx="50" cy="250" r="8" fill="#3b82f6" stroke="white" strokeWidth="2" />
                {/* Work marker */}
                <g transform="translate(350, 50)">
                  <rect x="-30" y="-12" width="60" height="24" rx="4" fill="#22c55e" />
                  <text x="0" y="5" textAnchor="middle" fill="white" fontSize="12" fontWeight="600">Work</text>
                </g>
                <circle cx="350" cy="50" r="6" fill="#22c55e" stroke="white" strokeWidth="2" />
              </svg>

              {/* Bus Stop Markers */}
              <div className="absolute" style={{ top: '45%', left: '35%' }}>
                <div className="w-8 h-8 bg-white rounded-lg shadow-lg flex items-center justify-center">
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
                    <path d="M8 6v6" />
                    <path d="M15 6v6" />
                    <path d="M2 12h19.6" />
                    <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                    <circle cx="7" cy="18" r="2" />
                    <path d="M9 18h5" />
                    <circle cx="16" cy="18" r="2" />
                  </svg>
                </div>
              </div>

              <div className="absolute" style={{ top: '55%', left: '55%' }}>
                <div className="w-8 h-8 bg-white rounded-lg shadow-lg flex items-center justify-center">
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
                    <path d="M8 6v6" />
                    <path d="M15 6v6" />
                    <path d="M2 12h19.6" />
                    <path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3" />
                    <circle cx="7" cy="18" r="2" />
                    <path d="M9 18h5" />
                    <circle cx="16" cy="18" r="2" />
                  </svg>
                </div>
              </div>

              {/* Duration Info Bar */}
              <div className="absolute bottom-4 left-4 right-4">
                <div className="bg-white rounded-xl shadow-lg p-4 flex items-center justify-between">
                  <div className="flex items-center gap-4">
                    <div className="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
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
                        className="text-emerald-600"
                      >
                        <path d="m5 12 7-7 7 7" />
                        <path d="M12 19V5" />
                      </svg>
                    </div>
                    <div>
                      <p className="text-xs text-neutral-500 uppercase tracking-wide">Estimated Duration</p>
                      <p className="text-lg font-bold text-neutral-800">
                        45 mins <span className="font-normal text-neutral-500">via Route AB</span>
                      </p>
                    </div>
                  </div>
                  <button
                    type="button"
                    className="px-6 py-3 bg-emerald-500 text-white font-semibold rounded-full hover:bg-emerald-600 transition-colors"
                  >
                    Continue to Return
                  </button>
                </div>
              </div>

              {/* Zoom Controls */}
              <div className="absolute right-4 top-1/2 -translate-y-1/2 flex flex-col gap-2">
                <button
                  type="button"
                  className="w-10 h-10 bg-white rounded-lg shadow-lg flex items-center justify-center text-neutral-600 hover:text-neutral-800 transition-colors"
                  aria-label="Zoom in"
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
                    <line x1="12" x2="12" y1="5" y2="19" />
                    <line x1="5" x2="19" y1="12" y2="12" />
                  </svg>
                </button>
                <button
                  type="button"
                  className="w-10 h-10 bg-white rounded-lg shadow-lg flex items-center justify-center text-neutral-600 hover:text-neutral-800 transition-colors"
                  aria-label="Zoom out"
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
                    <line x1="5" x2="19" y1="12" y2="12" />
                  </svg>
                </button>
              </div>

              {/* Satellite Toggle */}
              <div className="absolute bottom-24 left-4">
                <button
                  type="button"
                  className="bg-neutral-800/80 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-2 hover:bg-neutral-800 transition-colors"
                >
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
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                    <line x1="3" x2="21" y1="9" y2="9" />
                    <line x1="3" x2="21" y1="15" y2="15" />
                    <line x1="9" x2="9" y1="3" y2="21" />
                    <line x1="15" x2="15" y1="3" y2="21" />
                  </svg>
                  Satellite
                </button>
              </div>

              {/* Support Button */}
              <div className="absolute bottom-24 right-4">
                <button
                  type="button"
                  className="w-10 h-10 bg-neutral-800 rounded-full shadow-lg flex items-center justify-center text-white hover:bg-neutral-700 transition-colors"
                  aria-label="Support"
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
  );
}
