<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\SearchRoutes;

use RouteManagement\Domain\Model\ValueObject\RouteStatus;
use RouteManagement\Domain\Repository\{RouteRepository, RouteSnapshotRepository, StopRepository};

/**
 * Listado de rutas para el buscador (demo: sin filtro geoespacial estricto).
 * Incluye rutas aprobadas con snapshot publicado.
 */
final readonly class SearchRoutesHandler
{
    public function __construct(
        private RouteRepository $routes,
        private RouteSnapshotRepository $snapshots,
        private StopRepository $stops,
    ) {}

    public function handle(): array
    {
        $results = [];
        foreach ($this->routes->findAll() as $route) {
            if ($route->status() !== RouteStatus::Approved) {
                continue;
            }
            $snapshot = $this->snapshots->findLatestPublished($route->id());
            if ($snapshot === null) {
                continue;
            }

            $assignedStop = null;
            foreach ($snapshot->stops() as $rs) {
                $stop = $this->stops->findById($rs->stopId());
                if ($stop !== null) {
                    $assignedStop = [
                        'stopId' => $stop->id()->value,
                        'name' => $stop->name(),
                        'knownTitle' => $rs->alias(),
                        'latitude' => $stop->location()->lat,
                        'longitude' => $stop->location()->lon,
                    ];
                    break;
                }
            }

            $results[] = [
                'routeId' => $route->id()->value,
                'snapshotId' => $snapshot->id()->value,
                'title' => $route->name(),
                'schedules' => [],
                'hasMultipleSchedules' => false,
                'assignedStop' => $assignedStop,
            ];
        }

        return ['results' => $results];
    }
}
