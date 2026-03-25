<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\FindNearbyStops;

final readonly class FindNearbyStopsQuery
{
    public function __construct(
        public readonly float $lat,
        public readonly float $lon,
        public readonly float $radiusMeters = 2000.0,
    ) {}
}
