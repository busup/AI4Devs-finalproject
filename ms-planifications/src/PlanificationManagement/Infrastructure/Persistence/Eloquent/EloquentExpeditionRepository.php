<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use PlanificationManagement\Domain\Model\{AllocationRule, CapacityRule, Expedition, ExpeditionStop};
use PlanificationManagement\Domain\Model\ValueObject\{
    AllocationRuleId, CapacityRuleId, DaysOfWeek, ExpeditionId, ExpeditionStatus,
    ExpeditionStopId, RouteSnapshotRefId, SequenceOrder, StopLogicalId, TimeOfDay
};
use PlanificationManagement\Domain\Repository\ExpeditionRepository;

final class EloquentExpeditionRepository implements ExpeditionRepository
{
    public function findById(ExpeditionId $id): ?Expedition
    {
        $model = EloquentExpedition::with(['stops', 'capacityRules', 'allocationRules'])
            ->find($id->value);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findAll(): array
    {
        return EloquentExpedition::with(['stops', 'capacityRules', 'allocationRules'])
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(Expedition $expedition): void
    {
        $model = EloquentExpedition::updateOrCreate(
            ['id' => $expedition->id()->value],
            [
                'name'                  => $expedition->name(),
                'route_snapshot_ref_id' => $expedition->routeSnapshotRefId()->value,
                'days_of_week'          => $expedition->daysOfWeek()->bitmask,
                'base_time'             => $expedition->baseTime()->value,
                'status'                => $expedition->status()->value,
                'metadata'              => $expedition->metadata(),
                'created_at'            => $expedition->createdAt(),
                'updated_at'            => $expedition->updatedAt(),
                'deleted_at'            => $expedition->deletedAt(),
            ]
        );

        // Sync Stops
        $model->stops()->delete();
        foreach ($expedition->stops() as $stop) {
            $model->stops()->create([
                'id'               => $stop->id()->value,
                'stop_logical_id'  => $stop->stopLogicalId()->value,
                'sequence_order'   => $stop->sequenceOrder()->value,
                'offset_seconds'   => $stop->offsetSeconds(),
                'active'           => $stop->active(),
                'pickup_allowed'   => $stop->pickupAllowed(),
                'dropoff_allowed'  => $stop->dropoffAllowed(),
            ]);
        }

        // Sync Capacity Rules
        $model->capacityRules()->delete();
        foreach ($expedition->capacityRules() as $rule) {
            $model->capacityRules()->create([
                'id'                  => $rule->id()->value,
                'max_seats'           => $rule->maxSeats(),
                'segment_rules'       => $rule->segmentRules(),
                'client_restrictions' => $rule->clientRestrictions(),
                'active'              => $rule->active(),
            ]);
        }

        // Sync Allocation Rules
        $model->allocationRules()->delete();
        foreach ($expedition->allocationRules() as $rule) {
            $model->allocationRules()->create([
                'id'         => $rule->id()->value,
                'rule_type'  => $rule->ruleType(),
                'parameters' => $rule->parameters(),
                'priority'   => $rule->priority(),
                'active'     => $rule->active(),
            ]);
        }
    }

    public function delete(ExpeditionId $id): void
    {
        // soft delete based on the logical entity
        $model = EloquentExpedition::find($id->value);
        if ($model) {
            $model->update(['deleted_at' => now()]);
        }
    }

    private function toDomain(EloquentExpedition $model): Expedition
    {
        $stops = $model->stops->map(fn($s) => new ExpeditionStop(
            ExpeditionStopId::fromString($s->id),
            ExpeditionId::fromString($s->expedition_id),
            StopLogicalId::fromString($s->stop_logical_id),
            new SequenceOrder((int)$s->sequence_order),
            (int)$s->offset_seconds,
            $s->active,
            $s->pickup_allowed,
            $s->dropoff_allowed,
        ))->toArray();

        $cr = $model->capacityRules->map(fn($r) => new CapacityRule(
            CapacityRuleId::fromString($r->id),
            ExpeditionId::fromString($r->expedition_id),
            (int)$r->max_seats,
            $r->segment_rules,
            $r->client_restrictions,
            $r->active,
        ))->toArray();

        $ar = $model->allocationRules->map(fn($r) => new AllocationRule(
            AllocationRuleId::fromString($r->id),
            ExpeditionId::fromString($r->expedition_id),
            $r->rule_type,
            $r->parameters,
            (int)$r->priority,
            $r->active,
        ))->toArray();

        return Expedition::reconstitute(
            ExpeditionId::fromString($model->id),
            $model->name,
            RouteSnapshotRefId::fromString($model->route_snapshot_ref_id),
            new DaysOfWeek((int)$model->days_of_week),
            new TimeOfDay($model->base_time),
            ExpeditionStatus::from($model->status),
            $model->metadata,
            $model->created_at->toImmutable(),
            $model->updated_at->toImmutable(),
            $model->deleted_at?->toImmutable(),
            $stops,
            $cr,
            $ar,
        );
    }
}
