<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\CreatePlanification;

final readonly class CreatePlanificationCommand
{
    /**
     * @param string $expeditionId
     * @param string $dateFrom   Format: Y-m-d
     * @param string $dateUntil  Format: Y-m-d
     * @param array  $exceptions Array of Y-m-d strings
     * @param array  $nonWorkingDays Array of Y-m-d strings
     */
    public function __construct(
        public readonly string $expeditionId,
        public readonly string $dateFrom,
        public readonly string $dateUntil,
        public readonly array  $exceptions = [],
        public readonly array  $nonWorkingDays = [],
    ) {}
}
