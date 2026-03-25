<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

final readonly class Coordinates
{
    public function __construct(
        public readonly float $lat,
        public readonly float $lon,
    ) {
        if ($lat < -90.0 || $lat > 90.0) {
            throw new \InvalidArgumentException("Latitude {$lat} is out of range [-90, 90].");
        }
        if ($lon < -180.0 || $lon > 180.0) {
            throw new \InvalidArgumentException("Longitude {$lon} is out of range [-180, 180].");
        }
    }

    /** Haversine distance in metres. */
    public function distanceTo(self $other): float
    {
        $earthRadius = 6_371_000.0;
        $dLat = deg2rad($other->lat - $this->lat);
        $dLon = deg2rad($other->lon - $this->lon);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->lat)) * cos(deg2rad($other->lat)) * sin($dLon / 2) ** 2;

        return 2.0 * $earthRadius * asin(sqrt($a));
    }

    public function toWkt(): string
    {
        // WKT uses (lon lat) order
        return "POINT({$this->lon} {$this->lat})";
    }
}
