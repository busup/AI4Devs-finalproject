<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{CapacityRuleId, ExpeditionId};

final class CapacityRule
{
    public function __construct(
        private readonly CapacityRuleId  $id,
        private readonly ExpeditionId    $expeditionId,
        private int                      $maxSeats,
        private ?array                   $segmentRules       = null,
        private ?array                   $clientRestrictions = null,
        private bool                     $active             = true,
    ) {}

    public function id(): CapacityRuleId          { return $this->id; }
    public function expeditionId(): ExpeditionId  { return $this->expeditionId; }
    public function maxSeats(): int               { return $this->maxSeats; }
    public function segmentRules(): ?array        { return $this->segmentRules; }
    public function clientRestrictions(): ?array  { return $this->clientRestrictions; }
    public function active(): bool                { return $this->active; }
}
