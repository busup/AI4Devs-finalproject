<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\PlanGeneration\ProcessPlanGenerationJob;

final readonly class ProcessPlanGenerationJobCommand
{
    public function __construct(
        public readonly string $jobId,
    ) {}
}
