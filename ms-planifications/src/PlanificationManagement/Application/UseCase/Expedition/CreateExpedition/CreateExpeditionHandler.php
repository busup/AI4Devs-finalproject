<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\CreateExpedition;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\{CapacityRule, Expedition, ExpeditionStop};
use PlanificationManagement\Domain\Model\ValueObject\{
    CapacityRuleId, DaysOfWeek, ExpeditionId, ExpeditionStopId, 
    RouteSnapshotRefId, SequenceOrder, StopLogicalId, TimeOfDay
};
use PlanificationManagement\Domain\Repository\ExpeditionRepository;
use PlanificationManagement\Domain\Service\RouteSnapshotValidator;

final readonly class CreateExpeditionHandler
{
    public function __construct(
        private ExpeditionRepository   $expeditions,
        private RouteSnapshotValidator $routeValidator,
    ) {}

    public function handle(CreateExpeditionCommand $command): string
    {
        $id      = ExpeditionId::fromString(Str::uuid()->toString());
        $routeRef= RouteSnapshotRefId::fromString($command->routeSnapshotRefId);

        // Validates with ms-router synchronously!
        $this->routeValidator->validate($routeRef);

        $expedition = Expedition::create(
            $id,
            $command->name,
            $routeRef,
            new DaysOfWeek($command->daysOfWeekBitmask),
            new TimeOfDay($command->baseTime),
            $command->metadata,
        );

        foreach ($command->stops as $stopData) {
            $expedition->addStop(new ExpeditionStop(
                ExpeditionStopId::fromString(Str::uuid()->toString()),
                $id,
                StopLogicalId::fromString($stopData['stopLogicalId']),
                new SequenceOrder($stopData['sequenceOrder']),
                $stopData['offsetSeconds'],
                $stopData['active'] ?? true,
                $stopData['pickupAllowed'] ?? true,
                $stopData['dropoffAllowed'] ?? true,
            ));
        }

        foreach ($command->capacityRules as $cr) {
            $expedition->addCapacityRule(new CapacityRule(
                CapacityRuleId::fromString(Str::uuid()->toString()),
                $id,
                $cr['maxSeats'],
            ));
        }

        DB::transaction(function () use ($expedition) {
            $this->expeditions->save($expedition);
        });

        return $id->value;
    }
}
