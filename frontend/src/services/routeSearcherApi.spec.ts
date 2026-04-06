import { describe, expect, it } from 'vitest'

import { searchRoutes } from './routeSearcherApi'

describe('routeSearcherApi (mock)', () => {
  it('searchRoutes devuelve resultados vacíos en modo mock', async () => {
    const res = await searchRoutes({
      origin: { latitude: 40.4, longitude: -3.7 },
      destination: { latitude: 40.45, longitude: -3.68 },
      date: '2026-02-10',
      journeyType: 'outbound',
    })
    expect(res.results).toEqual([])
  })
})
