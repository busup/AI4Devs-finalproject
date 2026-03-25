<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\ActivatePlanification;

final readonly class ActivatePlanificationCommand
{
    public function __construct(
        public readonly string $planificationId,
    ) {}
}
