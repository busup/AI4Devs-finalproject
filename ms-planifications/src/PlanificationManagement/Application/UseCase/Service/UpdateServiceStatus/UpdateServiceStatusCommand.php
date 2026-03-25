<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Service\UpdateServiceStatus;

final readonly class UpdateServiceStatusCommand
{
    public function __construct(
        public readonly string  $serviceId,
        public readonly string  $status,
        public readonly ?string $reason = null,
    ) {}
}
