import axios, { type AxiosError, type AxiosInstance } from 'axios'

import type { SchedulesBySnapshotsResponse } from '@/types'

function getBaseUrl(): string {
  const url = import.meta.env.VITE_API_PLANIFICATIONS_BASE_URL
  if (!url?.trim()) {
    return ''
  }
  return url.replace(/\/$/, '')
}

export const planificationsClient: AxiosInstance = axios.create({
  baseURL: getBaseUrl(),
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

planificationsClient.interceptors.response.use(
  (res) => res,
  (err: AxiosError<{ message?: string }>) => {
    const status = err.response?.status
    const message = err.response?.data?.message ?? err.message
    return Promise.reject(
      new Error(status != null ? `[HTTP ${status}] ${message}` : message),
    )
  },
)

/**
 * POST /api/v1/schedules/by-snapshots — un servicio canónico por snapshot y fecha.
 * Sin `VITE_API_PLANIFICATIONS_BASE_URL`, no llama al backend (devuelve vacío).
 */
export async function fetchSchedulesBySnapshots(params: {
  date: string
  routeSnapshotIds: string[]
}): Promise<SchedulesBySnapshotsResponse> {
  const base = getBaseUrl()
  if (!base || params.routeSnapshotIds.length === 0) {
    return { schedules: {} }
  }
  const { data } = await planificationsClient.post<SchedulesBySnapshotsResponse>(
    '/schedules/by-snapshots',
    {
      date: params.date,
      routeSnapshotIds: params.routeSnapshotIds,
    },
  )
  return data
}
