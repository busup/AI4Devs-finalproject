import { apiClient } from './apiClient'
import { minimalSearchRoutesResponse } from '@/mocks/minimalSearchResponse'
import type {
  BookingRecord,
  CreateBookingRequest,
  CurrentUserResponse,
  RouteSchedulesParams,
  RouteSchedulesResponse,
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
 * GET /api/v1/routes/{routeId}/schedules
 */
export async function getRouteSchedules(
  routeId: string | number,
  params?: RouteSchedulesParams,
): Promise<RouteSchedulesResponse> {
  if (useMock()) {
    return { schedules: [] }
  }
  const { data } = await apiClient.get<RouteSchedulesResponse>(
    `/routes/${routeId}/schedules`,
    { params },
  )
  return data
}

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
