<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{
    SequenceOrder, ServiceId, ServiceStopId, StopLogicalId, TimeOfDay
};

final class ServiceStop
{
    public function __construct(
        private readonly ServiceStopId  $id,
        private readonly ServiceId      $serviceId,
        private readonly StopLogicalId  $stopLogicalId,
        private readonly SequenceOrder  $sequenceOrder,
        private readonly TimeOfDay      $scheduledTime,
        private bool                    $active         = true,
        private bool                    $pickupAllowed  = true,
        private bool                    $dropoffAllowed = true,
    ) {}

    public function id(): ServiceStopId              { return $this->id; }
    public function serviceId(): ServiceId           { return $this->serviceId; }
    public function stopLogicalId(): StopLogicalId   { return $this->stopLogicalId; }
    public function sequenceOrder(): SequenceOrder   { return $this->sequenceOrder; }
    public function scheduledTime(): TimeOfDay       { return $this->scheduledTime; }
    public function active(): bool                   { return $this->active; }
    public function pickupAllowed(): bool            { return $this->pickupAllowed; }
    public function dropoffAllowed(): bool           { return $this->dropoffAllowed; }
}
