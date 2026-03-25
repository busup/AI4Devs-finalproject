<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\GetExpedition;

final readonly class GetExpeditionQuery
{
    public function __construct(
        public readonly string $expeditionId,
    ) {}
}
