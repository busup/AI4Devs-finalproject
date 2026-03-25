<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSchedules;

use RouteManagement\Domain\Model\ValueObject\RouteId;
use RouteManagement\Domain\Repository\{RouteRepository, RouteSnapshotRepository};

final readonly class GetRouteSchedulesHandler
{
    public function __construct(
        private RouteRepository         $routes,
        private RouteSnapshotRepository $snapshots,
    ) {}

    public function handle(GetRouteSchedulesQuery $query): array
    {
        $routeId = RouteId::fromString($query->routeId);
        $route   = $this->routes->findById($routeId)
            ?? throw new \DomainException("Route {$query->routeId} not found.");

        $snapshot = $this->snapshots->findLatestPublished($routeId)
            ?? throw new \DomainException("No published snapshot for route {$query->routeId}.");

        $stops = array_map(
            static fn($rs) => [
                'stop_id'        => $rs->stopId()->value,
                'sequence_order' => $rs->sequenceOrder()->value,
                'dwell_time_s'   => $rs->dwellTimeS(),
                'alias'          => $rs->alias(),
                'pickup_allowed' => $rs->pickupAllowed(),
                'dropoff_allowed'=> $rs->dropoffAllowed(),
                'active'         => $rs->active(),
            ],
            $snapshot->stops(),
        );

        $geometries = array_map(
            static fn($rg) => [
                'type'    => $rg->geometryType()->value,
                'format'  => $rg->format()->value,
                'content' => $rg->content(),
            ],
            $snapshot->geometries(),
        );

        return [
            'route_id'       => $route->id()->value,
            'name'           => $route->name(),
            'status'         => $route->status()->value,
            'snapshot_id'    => $snapshot->id()->value,
            'version'        => $snapshot->versionNumber()->value,
            'valid_from'     => $snapshot->validFrom()?->format('Y-m-d'),
            'valid_until'    => $snapshot->validUntil()?->format('Y-m-d'),
            'published_at'   => $snapshot->publishedAt()?->format(\DateTimeInterface::ATOM),
            'total_distance_m'    => $snapshot->totalDistanceM(),
            'estimated_duration_s'=> $snapshot->estimatedDurationS(),
            'stops'          => $stops,
            'geometries'     => $geometries,
        ];
    }
}
