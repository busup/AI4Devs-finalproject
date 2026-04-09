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
  /** UUID en ms-router; el contrato legacy usaba número — ver docs/adr/0001-route-id-api-mapping.md */
  stopId: string | number
  name: string
  knownTitle?: string
  distance?: number
  latitude: number
  longitude: number
}

/** Horario de un servicio (ms-planifications `services.id` como track lógico). */
export interface RouteScheduleItem {
  trackId: string | number
  departureTime: string
  arrivalTime: string
  duration: number
}

/** Una parada con hora operativa del día (ms-planifications `service_stops`). */
export interface ServiceStopScheduleItem {
  stopId: string
  sequenceOrder: number
  /** HH:mm (Europe/Madrid, display). */
  scheduledTime: string
}

/** Servicio elegido para un snapshot en una fecha (respuesta batch ms-planifications). */
export interface RouteDaySchedule {
  serviceId: string
  departureTime: string
  arrivalTime: string
  durationSeconds: number
  stops: ServiceStopScheduleItem[]
}

export interface SchedulesBySnapshotsResponse {
  schedules: Record<string, RouteDaySchedule | null>
}

export interface SearchRouteResultItem {
  /** UUID de `routes.id` en ms-router (string); compatibilidad numérica opcional */
  routeId: string | number
  /** UUID de `route_snapshots.id` — clave para horarios en ms-planifications. */
  snapshotId?: string
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

/** GET /api/v1/stops/route-terminals — última parada por ruta aprobada (alias o nombre). */
export interface RouteTerminalStopItem {
  stopId: string
  label: string
}

export interface RouteTerminalStopsResponse {
  stops: RouteTerminalStopItem[]
}

export interface RouteSchedulesParams {
  date?: string
}

export interface RouteSchedulesResponse {
  /** Contrato documentado en docs/4 — ajustar cuando el backend esté fijado. */
  schedules: RouteScheduleItem[]
}

/** Respuesta real de ms-router `GET /routes/{id}/schedules` (detalle de ruta + paradas + geometrías). */
export interface RouteStopDetail {
  stop_id: string
  sequence_order: number
  dwell_time_s?: number
  alias?: string | null
  name?: string
  address?: string | null
  latitude?: number
  longitude?: number
  pickup_allowed?: boolean
  dropoff_allowed?: boolean
  active?: boolean
}

export interface RouteGeometryDetail {
  type: string
  format: string
  content: string
}

export interface RouteDetailResponse {
  route_id: string
  name: string
  status: string
  snapshot_id: string
  version: number
  valid_from?: string | null
  valid_until?: string | null
  published_at?: string | null
  total_distance_m?: number | null
  estimated_duration_s?: number | null
  stops: RouteStopDetail[]
  geometries: RouteGeometryDetail[]
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
