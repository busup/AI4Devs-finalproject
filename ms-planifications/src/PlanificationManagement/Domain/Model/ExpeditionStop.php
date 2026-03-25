<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{
    ExpeditionId, ExpeditionStopId, SequenceOrder, StopLogicalId, TimeOfDay
};

final class ExpeditionStop
{
    public function __construct(
        private readonly ExpeditionStopId $id,
        private readonly ExpeditionId     $expeditionId,
        private readonly StopLogicalId    $stopLogicalId,
        private readonly SequenceOrder    $sequenceOrder,
        private int                       $offsetSeconds,
        private bool                      $active         = true,
        private bool                      $pickupAllowed  = true,
        private bool                      $dropoffAllowed = true,
    ) {}

    public function scheduledTime(TimeOfDay $baseTime): TimeOfDay
    {
        return $baseTime->addSeconds($this->offsetSeconds);
    }

    public function id(): ExpeditionStopId             { return $this->id; }
    public function expeditionId(): ExpeditionId       { return $this->expeditionId; }
    public function stopLogicalId(): StopLogicalId     { return $this->stopLogicalId; }
    public function sequenceOrder(): SequenceOrder     { return $this->sequenceOrder; }
    public function offsetSeconds(): int               { return $this->offsetSeconds; }
    public function active(): bool                     { return $this->active; }
    public function pickupAllowed(): bool              { return $this->pickupAllowed; }
    public function dropoffAllowed(): bool             { return $this->dropoffAllowed; }
}
