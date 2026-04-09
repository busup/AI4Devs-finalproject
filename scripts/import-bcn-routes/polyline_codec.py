"""Decode Google encoded polyline; approximate path length in metres (haversine sum)."""

from __future__ import annotations

import math
from typing import List, Tuple


def decode(encoded: str) -> List[Tuple[float, float]]:
    """Returns list of (lat, lng) in degrees."""
    index, lat, lng = 0, 0, 0
    coordinates: List[Tuple[float, float]] = []
    while index < len(encoded):
        shift, result = 0, 0
        while True:
            b = ord(encoded[index]) - 63
            index += 1
            result |= (b & 0x1F) << shift
            shift += 5
            if b < 0x20:
                break
        dlat = ~(result >> 1) if result & 1 else (result >> 1)
        lat += dlat

        shift, result = 0, 0
        while True:
            b = ord(encoded[index]) - 63
            index += 1
            result |= (b & 0x1F) << shift
            shift += 5
            if b < 0x20:
                break
        dlng = ~(result >> 1) if result & 1 else (result >> 1)
        lng += dlng

        coordinates.append((lat * 1e-5, lng * 1e-5))
    return coordinates


def haversine_m(a: Tuple[float, float], b: Tuple[float, float]) -> float:
    """Great-circle distance between two (lat, lng) points in metres."""
    r = 6371000.0
    p1, l1 = math.radians(a[0]), math.radians(a[1])
    p2, l2 = math.radians(b[0]), math.radians(b[1])
    dp, dl = p2 - p1, l2 - l1
    h = math.sin(dp / 2) ** 2 + math.cos(p1) * math.cos(p2) * math.sin(dl / 2) ** 2
    return 2 * r * math.asin(min(1.0, math.sqrt(h)))


def path_length_m(coords: List[Tuple[float, float]]) -> float:
    if len(coords) < 2:
        return 0.0
    total = 0.0
    for i in range(1, len(coords)):
        total += haversine_m(coords[i - 1], coords[i])
    return total
