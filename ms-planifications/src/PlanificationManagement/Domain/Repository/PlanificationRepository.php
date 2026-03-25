<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Repository;

use PlanificationManagement\Domain\Model\Planification;
use PlanificationManagement\Domain\Model\ValueObject\{ExpeditionId, PlanificationId};

interface PlanificationRepository
{
    public function findById(PlanificationId $id): ?Planification;

    /** @return Planification[] */
    public function findByExpedition(ExpeditionId $expeditionId): array;

    /** @return Planification[] */
    public function findActiveWithFutureEndDate(\DateTimeImmutable $thresholdDate): array;

    public function save(Planification $planification): void;
}
