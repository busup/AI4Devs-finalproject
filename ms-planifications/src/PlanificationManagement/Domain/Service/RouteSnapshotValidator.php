<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Service;

use PlanificationManagement\Domain\Model\ValueObject\RouteSnapshotRefId;

interface RouteSnapshotValidator
{
    /**
     * Valida si el RouteSnapshot existe en el BoundedContext RouteManagement.
     * @throws \DomainException Si no existe o no se puede validar.
     */
    public function validate(RouteSnapshotRefId $refId): void;
}
