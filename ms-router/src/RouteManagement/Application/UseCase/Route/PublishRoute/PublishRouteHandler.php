<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\PublishRoute;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RouteManagement\Domain\Model\RouteSnapshot;
use RouteManagement\Domain\Model\RouteStop;
use RouteManagement\Domain\Model\ValueObject\{
    RouteId, RouteSnapshotId, RouteStopId, SequenceOrder, StopId, VersionNumber
};
use RouteManagement\Domain\Repository\{RouteRepository, RouteSnapshotRepository};
use RouteManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class PublishRouteHandler
{
    public function __construct(
        private RouteRepository         $routes,
        private RouteSnapshotRepository $snapshots,
        private OutboxEventStore        $outbox,
    ) {}

    public function handle(PublishRouteCommand $command): string
    {
        $routeId = RouteId::fromString($command->routeId);
        $route   = $this->routes->findById($routeId)
            ?? throw new \DomainException("Route {$command->routeId} not found.");

        $nextVersion = $this->snapshots->nextVersionNumber($routeId);
        $snapshotId  = RouteSnapshotId::fromString(Str::uuid()->toString());

        $snapshot = RouteSnapshot::create(
            $snapshotId,
            $routeId,
            new VersionNumber($nextVersion),
            $command->validFrom  ? new \DateTimeImmutable($command->validFrom)  : null,
            $command->validUntil ? new \DateTimeImmutable($command->validUntil) : null,
        );

        foreach ($command->stopIds as $order => $stopIdStr) {
            $snapshot->addStop(new RouteStop(
                RouteStopId::fromString(Str::uuid()->toString()),
                $snapshotId,
                StopId::fromString($stopIdStr),
                new SequenceOrder($order),
            ));
        }

        $snapshot->publish(); // emits RoutePublished
        $route->publishSnapshot($snapshotId);

        DB::transaction(function () use ($route, $snapshot) {
            $this->snapshots->save($snapshot);
            $this->routes->save($route);
            $this->outbox->store($snapshot->pullDomainEvents());
        });

        return $snapshotId->value;
    }
}
