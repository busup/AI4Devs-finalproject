<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model;

use RouteManagement\Domain\Model\ValueObject\{RouteSnapshotId, RouteStopId, SequenceOrder, StopId};

final class RouteStop
{
    public function __construct(
        private readonly RouteStopId      $id,
        private readonly RouteSnapshotId  $routeSnapshotId,
        private readonly StopId           $stopId,
        private readonly SequenceOrder    $sequenceOrder,
        private int                       $dwellTimeS     = 0,
        private ?string                   $alias          = null,
        private bool                      $pickupAllowed  = true,
        private bool                      $dropoffAllowed = true,
        private bool                      $active         = true,
    ) {}

    public function id(): RouteStopId              { return $this->id; }
    public function routeSnapshotId(): RouteSnapshotId { return $this->routeSnapshotId; }
    public function stopId(): StopId               { return $this->stopId; }
    public function sequenceOrder(): SequenceOrder { return $this->sequenceOrder; }
    public function dwellTimeS(): int              { return $this->dwellTimeS; }
    public function alias(): ?string               { return $this->alias; }
    public function pickupAllowed(): bool          { return $this->pickupAllowed; }
    public function dropoffAllowed(): bool         { return $this->dropoffAllowed; }
    public function active(): bool                 { return $this->active; }
}
