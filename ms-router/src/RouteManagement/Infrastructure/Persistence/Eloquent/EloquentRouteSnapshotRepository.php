<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use RouteManagement\Domain\Model\{RouteGeometry, RouteSnapshot, RouteStop};
use RouteManagement\Domain\Model\ValueObject\{
    GeometryFormat, GeometryType, RouteId, RouteSnapshotId, RouteStopId, SequenceOrder, StopId, VersionNumber
};
use RouteManagement\Domain\Repository\RouteSnapshotRepository;

final class EloquentRouteSnapshotRepository implements RouteSnapshotRepository
{
    public function findById(RouteSnapshotId $id): ?RouteSnapshot
    {
        $model = EloquentRouteSnapshot::with(['stops', 'geometries'])->find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function findLatestPublished(RouteId $routeId): ?RouteSnapshot
    {
        $model = EloquentRouteSnapshot::with(['stops', 'geometries'])
            ->where('route_id', $routeId->value)
            ->whereNotNull('published_at')
            ->orderByDesc('version_number')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function nextVersionNumber(RouteId $routeId): int
    {
        $maxVersion = EloquentRouteSnapshot::where('route_id', $routeId->value)
            ->max('version_number');
            
        return $maxVersion ? (int)$maxVersion + 1 : 1;
    }

    public function save(RouteSnapshot $snapshot): void
    {
        $model = EloquentRouteSnapshot::updateOrCreate(
            ['id' => $snapshot->id()->value],
            [
                'route_id'             => $snapshot->routeId()->value,
                'version_number'       => $snapshot->versionNumber()->value,
                'total_distance_m'     => $snapshot->totalDistanceM(),
                'estimated_duration_s' => $snapshot->estimatedDurationS(),
                'valid_from'           => $snapshot->validFrom(),
                'valid_until'          => $snapshot->validUntil(),
                'published_at'         => $snapshot->publishedAt(),
                'created_at'           => $snapshot->createdAt(),
            ]
        );

        $model->stops()->delete();
        foreach ($snapshot->stops() as $stop) {
            $model->stops()->create([
                'id'              => $stop->id()->value,
                'stop_id'         => $stop->stopId()->value,
                'sequence_order'  => $stop->sequenceOrder()->value,
                'dwell_time_s'    => $stop->dwellTimeS(),
                'alias'           => $stop->alias(),
                'pickup_allowed'  => $stop->pickupAllowed(),
                'dropoff_allowed' => $stop->dropoffAllowed(),
                'active'          => $stop->active(),
            ]);
        }

        $model->geometries()->delete();
        foreach ($snapshot->geometries() as $geo) {
            $model->geometries()->create([
                'id'            => $geo->id()->value,
                'geometry_type' => $geo->geometryType()->value,
                'format'        => $geo->format()->value,
                'provider'      => $geo->provider(),
                'segment_index' => $geo->segmentIndex(),
                'accuracy_m'    => $geo->accuracyM(),
                'content'       => $geo->content(),
                'created_at'    => $geo->createdAt(),
            ]);
        }
    }

    private function toDomain(EloquentRouteSnapshot $m): RouteSnapshot
    {
        $stops = $m->stops->map(fn($s) => new RouteStop(
            RouteStopId::fromString($s->id),
            RouteSnapshotId::fromString($m->id),
            StopId::fromString($s->stop_id),
            new SequenceOrder((int)$s->sequence_order),
            (int)$s->dwell_time_s,
            $s->alias,
            (bool)$s->pickup_allowed,
            (bool)$s->dropoff_allowed,
            (bool)$s->active,
        ))->toArray();

        $geometries = $m->geometries->map(fn($g) => new RouteGeometry(
            RouteId::fromString($g->id), // using RouteId as underlying type for geometry id is acceptable alias here based on the UUID creation in our entity
            RouteSnapshotId::fromString($m->id),
            GeometryType::from($g->geometry_type),
            GeometryFormat::from($g->format),
            $g->content,
            $g->provider,
            $g->segment_index !== null ? (int)$g->segment_index : null,
            $g->accuracy_m !== null ? (int)$g->accuracy_m : null,
            $g->created_at->toImmutable()
        ))->toArray();

        return RouteSnapshot::reconstitute(
            RouteSnapshotId::fromString($m->id),
            RouteId::fromString($m->route_id),
            new VersionNumber((int)$m->version_number),
            $m->valid_from?->toImmutable(),
            $m->valid_until?->toImmutable(),
            $m->published_at?->toImmutable(),
            $m->created_at->toImmutable(),
            $m->total_distance_m !== null ? (float)$m->total_distance_m : null,
            $m->estimated_duration_s !== null ? (int)$m->estimated_duration_s : null,
            $stops,
            $geometries
        );
    }
}
