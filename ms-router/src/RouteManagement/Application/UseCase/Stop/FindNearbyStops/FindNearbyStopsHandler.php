<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\FindNearbyStops;

use RouteManagement\Domain\Model\ValueObject\Coordinates;
use RouteManagement\Domain\Repository\StopRepository;

final readonly class FindNearbyStopsHandler
{
    public function __construct(
        private StopRepository $stops,
    ) {}

    /** @return array<array{id:string,name:string,lat:float,lon:float,distance_m:float}> */
    public function handle(FindNearbyStopsQuery $query): array
    {
        $centre   = new Coordinates($query->lat, $query->lon);
        $nearby   = $this->stops->findNearby($centre, $query->radiusMeters);

        return array_map(
            static fn($stop) => [
                'id'         => $stop->id()->value,
                'name'       => $stop->name(),
                'address'    => $stop->address(),
                'lat'        => $stop->location()->lat,
                'lon'        => $stop->location()->lon,
                'timezone'   => $stop->timezone()->value,
                'accessible' => $stop->isAccessible(),
                'distance_m' => $centre->distanceTo($stop->location()),
            ],
            $nearby,
        );
    }
}
