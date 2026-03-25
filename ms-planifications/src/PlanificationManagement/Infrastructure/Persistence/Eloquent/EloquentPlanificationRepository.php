<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use PlanificationManagement\Domain\Model\Planification;
use PlanificationManagement\Domain\Model\ValueObject\{
    DateCollection, ExpeditionId, PlanificationId, PlanificationStatus
};
use PlanificationManagement\Domain\Repository\PlanificationRepository;

final class EloquentPlanificationRepository implements PlanificationRepository
{
    public function findById(PlanificationId $id): ?Planification
    {
        $model = EloquentPlanification::find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByExpedition(ExpeditionId $expeditionId): array
    {
        return EloquentPlanification::where('expedition_id', $expeditionId->value)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findActiveWithFutureEndDate(\DateTimeImmutable $thresholdDate): array
    {
        return EloquentPlanification::where('status', PlanificationStatus::Active->value)
            ->where('date_until', '>', $thresholdDate)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(Planification $planification): void
    {
        EloquentPlanification::updateOrCreate(
            ['id' => $planification->id()->value],
            [
                'expedition_id'    => $planification->expeditionId()->value,
                'date_from'        => $planification->dateFrom(),
                'date_until'       => $planification->dateUntil(),
                'exceptions'       => $planification->exceptions()->toArray(),
                'non_working_days' => $planification->nonWorkingDays()->toArray(),
                'status'           => $planification->status()->value,
                'created_at'       => $planification->createdAt(),
                'updated_at'       => $planification->updatedAt(),
            ]
        );
    }

    private function toDomain(EloquentPlanification $m): Planification
    {
        return Planification::reconstitute(
            PlanificationId::fromString($m->id),
            ExpeditionId::fromString($m->expedition_id),
            $m->date_from->toImmutable(),
            $m->date_until->toImmutable(),
            new DateCollection($m->exceptions ?? []),
            new DateCollection($m->non_working_days ?? []),
            PlanificationStatus::from($m->status),
            $m->created_at->toImmutable(),
            $m->updated_at->toImmutable(),
        );
    }
}
