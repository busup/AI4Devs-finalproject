<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Support\Facades\DB;
use RouteManagement\Domain\Model\Stop;
use RouteManagement\Domain\Model\ValueObject\{
    ApprovalStatus, Coordinates, StopId, Timezone
};
use RouteManagement\Domain\Repository\StopRepository;

final class EloquentStopRepository implements StopRepository
{
    public function findById(StopId $id): ?Stop
    {
        $model = EloquentStop::find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function findNearby(Coordinates $centre, float $radiusMeters): array
    {
        // Using MySQL ST_Distance_Sphere for precise calculation
        $stops = EloquentStop::whereRaw("
            ST_Distance_Sphere(location, ST_GeomFromText('POINT(? ?)', 4326)) <= ?
        ", [$centre->lon, $centre->lat, $radiusMeters])
            ->where('approval_status', ApprovalStatus::Approved->value)
            ->get();

        return $stops->map(fn($m) => $this->toDomain($m))->toArray();
    }

    public function save(Stop $stop): void
    {
        EloquentStop::updateOrCreate(
            ['id' => $stop->id()->value],
            [
                'name'            => $stop->name(),
                'address'         => $stop->address(),
                'lat'             => $stop->location()->lat,
                'lon'             => $stop->location()->lon,
                // Updating spatial column correctly for MySQL > 8
                'location'        => DB::raw("ST_GeomFromText('POINT({$stop->location()->lon} {$stop->location()->lat})', 4326)"),
                'timezone'        => $stop->timezone()->value,
                'is_accessible'   => $stop->isAccessible(),
                'approval_status' => $stop->approvalStatus()->value,
                'metadata'        => $stop->metadata(),
                'created_at'      => $stop->createdAt(),
                'updated_at'      => $stop->updatedAt(),
            ]
        );
    }

    private function toDomain(EloquentStop $m): Stop
    {
        return Stop::reconstitute(
            StopId::fromString($m->id),
            $m->name,
            $m->address,
            new Coordinates((float)$m->lat, (float)$m->lon),
            new Timezone($m->timezone),
            (bool)$m->is_accessible,
            ApprovalStatus::from($m->approval_status),
            $m->metadata,
            $m->created_at->toImmutable(),
            $m->updated_at->toImmutable()
        );
    }
}
