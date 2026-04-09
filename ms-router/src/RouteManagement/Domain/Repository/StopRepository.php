<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Repository;

use RouteManagement\Domain\Model\Stop;
use RouteManagement\Domain\Model\ValueObject\{Coordinates, StopId};

interface StopRepository
{
    public function findById(StopId $id): ?Stop;

    /**
     * @param  string[]  $ids  UUID strings
     * @return array<string, Stop> keyed by stop id
     */
    public function findByIds(array $ids): array;

    /**
     * Find approved, non-deleted stops within a given radius.
     *
     * @param  Coordinates $coordinates  Centre point
     * @param  float       $radiusMeters Maximum distance in metres
     * @return Stop[]
     */
    public function findNearby(Coordinates $coordinates, float $radiusMeters): array;

    public function save(Stop $stop): void;

    public function delete(StopId $id): void;
}
