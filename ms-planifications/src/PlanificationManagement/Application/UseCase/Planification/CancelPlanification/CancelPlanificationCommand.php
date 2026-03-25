<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\CancelPlanification;

final readonly class CancelPlanificationCommand
{
    public function __construct(
        public readonly string $planificationId,
    ) {}
}
