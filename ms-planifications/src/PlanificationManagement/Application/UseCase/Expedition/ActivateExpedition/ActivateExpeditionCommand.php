<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\ActivateExpedition;

final readonly class ActivateExpeditionCommand
{
    public function __construct(
        public readonly string $expeditionId,
    ) {}
}
