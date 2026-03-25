<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Repository;

use PlanificationManagement\Domain\Model\PlanGenerationJob;
use PlanificationManagement\Domain\Model\ValueObject\PlanGenerationJobId;

interface PlanGenerationJobRepository
{
    public function findById(PlanGenerationJobId $id): ?PlanGenerationJob;

    /** @return PlanGenerationJob[] */
    public function findPending(): array;

    public function save(PlanGenerationJob $job): void;
}
