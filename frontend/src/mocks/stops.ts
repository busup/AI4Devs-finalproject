/** Paradas mock hasta existir API de sitio / router. */

export interface MockStop {
  id: string
  name: string
}

export const MOCK_STOPS: MockStop[] = [
  { id: 'stop-1', name: 'Sede central — Entrada principal' },
  { id: 'stop-2', name: 'Parque empresarial — Glorieta Norte' },
  { id: 'stop-3', name: 'Metro Moncloa — Intercambiador' },
  { id: 'stop-4', name: 'Las Rozas — Avenida de la Industria' },
]
