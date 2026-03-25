<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\GetExpedition;

use PlanificationManagement\Domain\Model\ValueObject\ExpeditionId;
use PlanificationManagement\Domain\Repository\ExpeditionRepository;

final readonly class GetExpeditionHandler
{
    public function __construct(
        private ExpeditionRepository $expeditions,
    ) {}

    public function handle(GetExpeditionQuery $query): array
    {
        $id = ExpeditionId::fromString($query->expeditionId);
        $expedition = $this->expeditions->findById($id)
            ?? throw new \DomainException("Expedition {$query->expeditionId} not found.");

        $stops = array_map(
            static fn($s) => [
                'id'              => $s->id()->value,
                'stop_logical_id' => $s->stopLogicalId()->value,
                'sequence_order'  => $s->sequenceOrder()->value,
                'offset_seconds'  => $s->offsetSeconds(),
                'active'          => $s->active(),
                'pickup_allowed'  => $s->pickupAllowed(),
                'dropoff_allowed' => $s->dropoffAllowed(),
            ],
            $expedition->stops()
        );

        $capacityRules = array_map(
            static fn($r) => [
                'id'        => $r->id()->value,
                'max_seats' => $r->maxSeats(),
                'active'    => $r->active(),
            ],
            $expedition->capacityRules()
        );

        return [
            'id'                   => $expedition->id()->value,
            'name'                 => $expedition->name(),
            'route_snapshot_ref_id'=> $expedition->routeSnapshotRefId()->value,
            'days_of_week'         => $expedition->daysOfWeek()->bitmask,
            'base_time'            => $expedition->baseTime()->value,
            'status'               => $expedition->status()->value,
            'stops'                => $stops,
            'capacity_rules'       => $capacityRules,
            'created_at'           => $expedition->createdAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
