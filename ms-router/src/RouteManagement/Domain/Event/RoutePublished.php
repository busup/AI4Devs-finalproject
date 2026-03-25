<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Event;

use RouteManagement\Domain\Model\ValueObject\{RouteId, RouteSnapshotId, VersionNumber};

final readonly class RoutePublished
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly RouteId         $routeId,
        public readonly RouteSnapshotId $snapshotId,
        public readonly VersionNumber   $versionNumber,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'RoutePublished'; }

    public function aggregateType(): string { return 'Route'; }

    public function aggregateId(): string { return $this->routeId->value; }

    public function toPayload(): array
    {
        return [
            'route_id'       => $this->routeId->value,
            'snapshot_id'    => $this->snapshotId->value,
            'version_number' => $this->versionNumber->value,
            'occurred_at'    => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
