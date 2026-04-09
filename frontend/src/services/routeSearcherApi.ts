import { apiClient } from './apiClient'
import { minimalSearchRoutesResponse } from '@/mocks/minimalSearchResponse'
import { MOCK_STOPS } from '@/mocks/stops'
import type {
  BookingRecord,
  CreateBookingRequest,
  CurrentUserResponse,
  RouteDetailResponse,
  RouteSchedulesParams,
  RouteTerminalStopsResponse,
  SearchRoutesRequest,
  SearchRoutesResponse,
  SiteConfigResponse,
} from '@/types'

function useMock(): boolean {
  return import.meta.env.VITE_USE_MOCK_API === 'true'
}

/**
 * POST /api/v1/search/routes
 */
export async function searchRoutes(
  body: SearchRoutesRequest,
): Promise<SearchRoutesResponse> {
  if (useMock()) {
    return Promise.resolve({ ...minimalSearchRoutesResponse })
  }
  const { data } = await apiClient.post<SearchRoutesResponse>(
    '/search/routes',
    body,
  )
  return data
}

/**
 * GET /api/v1/stops/route-terminals — paradas finales (una por stop físico, etiqueta desde BD).
 */
export async function getRouteTerminalStops(): Promise<RouteTerminalStopsResponse> {
  if (useMock()) {
    return {
      stops: MOCK_STOPS.map((s) => ({ stopId: s.id, label: s.name })),
    }
  }
  const { data } = await apiClient.get<RouteTerminalStopsResponse>('/stops/route-terminals')
  return data
}

/**
 * GET /api/v1/routes/{routeId}/schedules — detalle de ruta, paradas y geometrías (ms-router).
 */
export async function getRouteDetail(
  routeId: string | number,
  params?: RouteSchedulesParams,
): Promise<RouteDetailResponse> {
  if (useMock()) {
    return {
      route_id: String(routeId),
      name: '',
      status: 'approved',
      snapshot_id: '',
      version: 1,
      stops: [],
      geometries: [],
    }
  }
  const { data } = await apiClient.get<RouteDetailResponse>(
    `/routes/${routeId}/schedules`,
    { params },
  )
  return data
}

/** Alias histórico; la respuesta real es {@link RouteDetailResponse}. */
export const getRouteSchedules = getRouteDetail

/**
 * GET /api/v1/sites/{siteId}/config
 */
export async function getSiteConfig(siteId: string | number): Promise<SiteConfigResponse> {
  if (useMock()) {
    return { siteId: Number(siteId), searchRadiusMeters: 5000 }
  }
  const { data } = await apiClient.get<SiteConfigResponse>(
    `/sites/${siteId}/config`,
  )
  return data
}

/**
 * POST /api/v1/bookings
 */
export async function createBooking(
  body: CreateBookingRequest,
): Promise<BookingRecord> {
  if (useMock()) {
    return {
      id: 'mock-booking-id',
      status: 'pending',
      routeId: body.routeId,
      trackId: body.trackId,
      date: body.date,
      journeyType: body.journeyType,
    }
  }
  const { data } = await apiClient.post<BookingRecord>('/bookings', body)
  return data
}

/**
 * GET /api/v1/bookings
 */
export async function listBookings(): Promise<BookingRecord[]> {
  if (useMock()) {
    return []
  }
  const { data } = await apiClient.get<BookingRecord[]>('/bookings')
  return data
}

/**
 * GET /api/v1/users/me
 */
export async function getCurrentUser(): Promise<CurrentUserResponse> {
  if (useMock()) {
    return { id: 'mock-user' }
  }
  const { data } = await apiClient.get<CurrentUserResponse>('/users/me')
  return data
}
