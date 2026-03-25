<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Event;

use RouteManagement\Domain\Model\ValueObject\RouteSnapshotId;

final readonly class RouteEstimateUpdated
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly RouteSnapshotId $snapshotId,
        public readonly int             $totalDistanceM,
        public readonly int             $estimatedDurationS,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'RouteEstimateUpdated'; }

    public function aggregateType(): string { return 'RouteSnapshot'; }

    public function aggregateId(): string { return $this->snapshotId->value; }

    public function toPayload(): array
    {
        return [
            'route_snapshot_id'   => $this->snapshotId->value,
            'total_distance_m'    => $this->totalDistanceM,
            'estimated_duration_s'=> $this->estimatedDurationS,
            'occurred_at'         => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
