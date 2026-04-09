/** Modelo de UI de búsqueda (alineable con `SearchRoutesRequest` en `types/api.ts`). */

export type TripMode = 'outbound_only' | 'return_only' | 'roundtrip'

export type TimeFilterMode = 'departure' | 'arrival'

export interface TimeFilter {
  mode: TimeFilterMode
  /** HH:mm */
  hhmm: string
}
