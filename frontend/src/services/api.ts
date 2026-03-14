/**
 * API client - calls the API Gateway at VITE_API_BASE_URL.
 * All /api/v1/* requests are proxied to the corresponding microservice.
 */

const baseUrl = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8080/api/v1'

export const api = {
  async health(): Promise<{ message?: string }> {
    const res = await fetch(`${baseUrl}/search/health`, { method: 'GET' })
    if (!res.ok) throw new Error(res.statusText)
    return res.json()
  },
}
