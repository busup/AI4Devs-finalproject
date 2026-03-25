<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\ActivateExpedition;

use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\ValueObject\ExpeditionId;
use PlanificationManagement\Domain\Repository\ExpeditionRepository;

final readonly class ActivateExpeditionHandler
{
    public function __construct(
        private ExpeditionRepository $expeditions,
    ) {}

    public function handle(ActivateExpeditionCommand $command): void
    {
        $id = ExpeditionId::fromString($command->expeditionId);
        $expedition = $this->expeditions->findById($id)
            ?? throw new \DomainException("Expedition {$command->expeditionId} not found.");

        $expedition->activate();

        DB::transaction(function () use ($expedition) {
            $this->expeditions->save($expedition);
        });
    }
}
