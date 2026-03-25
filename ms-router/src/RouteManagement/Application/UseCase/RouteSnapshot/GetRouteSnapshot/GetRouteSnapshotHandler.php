<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSnapshot;

use RouteManagement\Domain\Model\ValueObject\RouteSnapshotId;
use RouteManagement\Domain\Repository\RouteSnapshotRepository;

final readonly class GetRouteSnapshotHandler
{
    public function __construct(private RouteSnapshotRepository $repository) {}

    public function handle(GetRouteSnapshotQuery $query): ?array
    {
        $snapshot = $this->repository->findById(RouteSnapshotId::fromString($query->snapshotId));

        if (!$snapshot) {
            return null;
        }

        return [
            'id' => $snapshot->id()->value,
            'route_id' => $snapshot->routeId()->value,
            'version' => $snapshot->versionNumber()->value,
        ];
    }
}
