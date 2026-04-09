<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\ListRouteTerminalStops;

use RouteManagement\Domain\Model\RouteStop;
use RouteManagement\Domain\Model\ValueObject\RouteStatus;
use RouteManagement\Domain\Repository\{RouteRepository, RouteSnapshotRepository, StopRepository};

/**
 * Última parada (mayor sequence_order) de cada ruta aprobada con snapshot publicado.
 * Etiqueta para UI: alias de la parada en la ruta si existe; si no, nombre del stop en ms-router.
 */
final readonly class ListRouteTerminalStopsHandler
{
    public function __construct(
        private RouteRepository $routes,
        private RouteSnapshotRepository $snapshots,
        private StopRepository $stops,
    ) {}

    /** @return array{stops: list<array{stopId: string, label: string}>} */
    public function handle(): array
    {
        $byStopId = [];

        foreach ($this->routes->findAll() as $route) {
            if ($route->status() !== RouteStatus::Approved) {
                continue;
            }
            $snapshot = $this->snapshots->findLatestPublished($route->id());
            if ($snapshot === null) {
                continue;
            }
            $last = $this->lastRouteStop($snapshot->stops());
            if ($last === null) {
                continue;
            }
            $sid = $last->stopId()->value;
            $stop = $this->stops->findById($last->stopId());
            $alias = $last->alias();
            $label = (is_string($alias) && trim($alias) !== '')
                ? trim($alias)
                : ($stop !== null ? $stop->name() : $sid);

            if (! isset($byStopId[$sid])) {
                $byStopId[$sid] = ['stopId' => $sid, 'label' => $label];

                continue;
            }
            if (strlen($label) > strlen($byStopId[$sid]['label'])) {
                $byStopId[$sid]['label'] = $label;
            }
        }

        $list = array_values($byStopId);
        usort(
            $list,
            static fn(array $a, array $b): int => strcasecmp($a['label'], $b['label']),
        );

        return ['stops' => $list];
    }

    /** @param RouteStop[] $stops */
    private function lastRouteStop(array $stops): ?RouteStop
    {
        $best = null;
        $max = -1;
        foreach ($stops as $rs) {
            $seq = $rs->sequenceOrder()->value;
            if ($seq > $max) {
                $max = $seq;
                $best = $rs;
            }
        }

        return $best;
    }
}
