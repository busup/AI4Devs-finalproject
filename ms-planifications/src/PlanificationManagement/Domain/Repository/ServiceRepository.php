<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Repository;

use PlanificationManagement\Domain\Model\Service;
use PlanificationManagement\Domain\Model\ValueObject\{PlanificationId, ServiceId};

interface ServiceRepository
{
    public function findById(ServiceId $id): ?Service;

    /** @return Service[] */
    public function findByPlanification(PlanificationId $planificationId): array;

    /** @return Service[] */
    public function findByDate(\DateTimeImmutable $date): array;

    public function save(Service $service): void;

    /** @param Service[] $services */
    public function saveMany(array $services): void;
}
