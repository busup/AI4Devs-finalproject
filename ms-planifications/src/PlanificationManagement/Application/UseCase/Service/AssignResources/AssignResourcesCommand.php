<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Service\AssignResources;

final readonly class AssignResourcesCommand
{
    public function __construct(
        public readonly string  $serviceId,
        public readonly ?string $vehicleLogicalId,
        public readonly ?string $driverLogicalId,
        public readonly ?string $provider,
    ) {}
}
