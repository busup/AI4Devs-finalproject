/**
 * Tipos alineados con docs/4_especificacion_api.md (evolucionar con el contrato real).
 */

export type JourneyType = 'outbound' | 'return' | 'roundtrip'

export interface GeoPoint {
  latitude: number
  longitude: number
  address?: string
}

export interface SearchRoutesRequest {
  origin: GeoPoint
  destination: GeoPoint
  siteId?: number
  date: string
  journeyType: JourneyType
  filters?: {
    departureAfter?: string
    arrivalBefore?: string
  }
}

export interface AssignedStop {
  stopId: number
  name: string
  knownTitle?: string
  distance?: number
  latitude: number
  longitude: number
}

export interface RouteScheduleItem {
  trackId: number
  departureTime: string
  arrivalTime: string
  duration: number
}

export interface SearchRouteResultItem {
  routeId: number
  title: string
  invitationCode?: string
  assignedStop?: AssignedStop
  schedules: RouteScheduleItem[]
  hasMultipleSchedules?: boolean
  additionalSchedulesCount?: number
}

export interface SearchRoutesResponse {
  results: SearchRouteResultItem[]
}

export interface RouteSchedulesParams {
  date?: string
}

export interface RouteSchedulesResponse {
  /** Contrato documentado en docs/4 — ajustar cuando el backend esté fijado. */
  schedules: RouteScheduleItem[]
}

export interface SiteConfigResponse {
  siteId: number
  searchRadiusMeters: number
  /** Ampliar según docs/4_especificacion_api.md */
  [key: string]: unknown
}

export interface CreateBookingRequest {
  /** Placeholder — completar con el contrato POST /bookings */
  routeId: number
  trackId: number
  date: string
  journeyType: JourneyType
}

export interface BookingRecord {
  id: string
  status: string
  routeId?: number
  trackId?: number
  date?: string
  journeyType?: JourneyType
  /** Ampliar según contrato */
  [key: string]: unknown
}

export interface CurrentUserResponse {
  id: string
  email?: string
  siteId?: number
  [key: string]: unknown
}
