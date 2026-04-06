import axios, { type AxiosError, type AxiosInstance } from 'axios'

function getBaseUrl(): string {
  const url = import.meta.env.VITE_API_BASE_URL
  if (!url) {
    console.warn('[apiClient] VITE_API_BASE_URL no está definida; las peticiones pueden fallar.')
    return ''
  }
  return url.replace(/\/$/, '')
}

export const apiClient: AxiosInstance = axios.create({
  baseURL: getBaseUrl(),
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

apiClient.interceptors.response.use(
  (res) => res,
  (err: AxiosError<{ message?: string }>) => {
    const status = err.response?.status
    const message = err.response?.data?.message ?? err.message
    return Promise.reject(
      new Error(
        status != null ? `[HTTP ${status}] ${message}` : message,
      ),
    )
  },
)
