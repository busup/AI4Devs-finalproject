import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface BookingSearchMock {
  origin: string
  destination: string
  date: string
  journeyType: 'outbound' | 'return' | 'roundtrip'
}

export interface JourneyLegMock {
  time: string
  from: string
  to: string
  arrivalTime?: string
  routeLabel?: string
  stopName?: string
  stopWalk?: string
}

export interface BookingSummaryMock {
  bookingId: string
  ticketRef: string
  routeId: string
  seatNumber: string
  travelDate: string
  passenger: string
  employeeId: string
  outbound: JourneyLegMock
  returnTrip: JourneyLegMock
  paymentLabel?: string
  paymentSublabel?: string
}

export const useBookingStore = defineStore('booking', () => {
  const search = ref<BookingSearchMock>({
    origin: 'Calle Gran Vía 28, Madrid',
    destination: 'Parque Empresarial Las Rozas, Madrid',
    date: '',
    journeyType: 'roundtrip',
  })

  const summary = ref<BookingSummaryMock | null>({
    bookingId: 'RT-84920',
    ticketRef: 'RT-84920-A12X',
    routeId: 'EXPRESS-042',
    seatNumber: '12A (Ventana)',
    travelDate: 'Viernes, 1 Dic 2024',
    passenger: 'Alex Thompson',
    employeeId: '#7729-RS',
    outbound: {
      time: '08:00',
      from: 'Origen',
      to: 'Destino',
      arrivalTime: '08:45',
      routeLabel: 'Route AB - Corporate Express',
      stopName: 'Oak Street Corner',
      stopWalk: '2 min walk from your location',
    },
    returnTrip: {
      time: '17:30',
      from: 'Destino',
      to: 'Origen',
      arrivalTime: '18:15',
      routeLabel: 'Route BA - Evening Direct',
      stopName: 'Main Office Gate B',
      stopWalk: '1 min walk from desk',
    },
    paymentLabel: 'Corporate Smartpass',
    paymentSublabel: 'Unlimited Commuting Plan',
  })

  function setSearch(data: Partial<BookingSearchMock>) {
    search.value = { ...search.value, ...data }
  }

  function setSummary(data: BookingSummaryMock | null) {
    summary.value = data
  }

  return { search, summary, setSearch, setSummary }
})
