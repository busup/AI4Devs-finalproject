<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{AllocationRuleId, ExpeditionId};

final class AllocationRule
{
    public function __construct(
        private readonly AllocationRuleId $id,
        private readonly ExpeditionId     $expeditionId,
        private string                    $ruleType,   // e.g. 'preferred_provider', 'max_vehicle_age'
        private array                     $parameters,
        private int                       $priority,
        private bool                      $active = true,
    ) {}

    public function id(): AllocationRuleId         { return $this->id; }
    public function expeditionId(): ExpeditionId   { return $this->expeditionId; }
    public function ruleType(): string             { return $this->ruleType; }
    public function parameters(): array            { return $this->parameters; }
    public function priority(): int                { return $this->priority; }
    public function active(): bool                 { return $this->active; }
}
