<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSnapshot;

final readonly class GetRouteSnapshotQuery
{
    public function __construct(public string $snapshotId) {}
}
