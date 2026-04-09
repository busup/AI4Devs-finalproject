/** Decodifica polyline encoded (algoritmo Google) → puntos WGS84. */

export interface LatLngLiteral {
  lat: number
  lng: number
}

export function decodeEncodedPolyline(encoded: string): LatLngLiteral[] {
  const coordinates: LatLngLiteral[] = []
  let index = 0
  let lat = 0
  let lng = 0

  while (index < encoded.length) {
    let b: number
    let shift = 0
    let result = 0
    do {
      b = encoded.charCodeAt(index++) - 63
      result |= (b & 0x1f) << shift
      shift += 5
    } while (b >= 0x20)
    const dlat = (result & 1) !== 0 ? ~(result >> 1) : result >> 1
    lat += dlat

    shift = 0
    result = 0
    do {
      b = encoded.charCodeAt(index++) - 63
      result |= (b & 0x1f) << shift
      shift += 5
    } while (b >= 0x20)
    const dlng = (result & 1) !== 0 ? ~(result >> 1) : result >> 1
    lng += dlng

    coordinates.push({ lat: lat / 1e5, lng: lng / 1e5 })
  }

  return coordinates
}
