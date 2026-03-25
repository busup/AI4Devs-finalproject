<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use RouteManagement\Domain\Model\Route;
use RouteManagement\Domain\Model\ValueObject\{RouteId, RouteSnapshotId, RouteStatus};
use RouteManagement\Domain\Repository\RouteRepository;

final class EloquentRouteRepository implements RouteRepository
{
    public function findById(RouteId $id): ?Route
    {
        $model = EloquentRoute::find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function save(Route $route): void
    {
        EloquentRoute::updateOrCreate(
            ['id' => $route->id()->value],
            [
                'name'                => $route->name(),
                'status'              => $route->status()->value,
                'current_snapshot_id' => $route->currentSnapshotId()?->value,
                'created_at'          => $route->createdAt(),
                'updated_at'          => $route->updatedAt(),
            ]
        );
    }

    private function toDomain(EloquentRoute $m): Route
    {
        $currentSnapshotId = $m->current_snapshot_id
            ? RouteSnapshotId::fromString($m->current_snapshot_id)
            : null;

        return Route::reconstitute(
            RouteId::fromString($m->id),
            $m->name,
            RouteStatus::from($m->status),
            $currentSnapshotId,
            $m->created_at->toImmutable(),
            $m->updated_at->toImmutable()
        );
    }
}
