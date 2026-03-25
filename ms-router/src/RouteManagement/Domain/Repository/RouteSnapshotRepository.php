<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Repository;

use RouteManagement\Domain\Model\RouteSnapshot;
use RouteManagement\Domain\Model\ValueObject\{RouteId, RouteSnapshotId};

interface RouteSnapshotRepository
{
    public function findById(RouteSnapshotId $id): ?RouteSnapshot;

    /** @return RouteSnapshot[] */
    public function findByRouteId(RouteId $routeId): array;

    public function findLatestPublished(RouteId $routeId): ?RouteSnapshot;

    /** Returns the next version number for a given route (max + 1). */
    public function nextVersionNumber(RouteId $routeId): int;

    public function save(RouteSnapshot $snapshot): void;
}
