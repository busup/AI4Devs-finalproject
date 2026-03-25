<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Repository;

use PlanificationManagement\Domain\Model\Expedition;
use PlanificationManagement\Domain\Model\ValueObject\ExpeditionId;

interface ExpeditionRepository
{
    public function findById(ExpeditionId $id): ?Expedition;

    /** @return Expedition[] */
    public function findAll(): array;

    public function save(Expedition $expedition): void;

    public function delete(ExpeditionId $id): void;
}
