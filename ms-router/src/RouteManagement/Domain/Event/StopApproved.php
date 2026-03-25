<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Event;

use RouteManagement\Domain\Model\ValueObject\{Coordinates, StopId};

final readonly class StopApproved
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly StopId      $stopId,
        public readonly Coordinates $location,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'StopApproved'; }

    public function aggregateType(): string { return 'Stop'; }

    public function aggregateId(): string { return $this->stopId->value; }

    public function toPayload(): array
    {
        return [
            'stop_id'     => $this->stopId->value,
            'lat'         => $this->location->lat,
            'lon'         => $this->location->lon,
            'occurred_at' => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
