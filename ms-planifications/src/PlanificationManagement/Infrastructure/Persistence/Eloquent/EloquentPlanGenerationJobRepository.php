<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use PlanificationManagement\Domain\Model\PlanGenerationJob;
use PlanificationManagement\Domain\Model\ValueObject\{JobStatus, PlanGenerationJobId, PlanificationId};
use PlanificationManagement\Domain\Repository\PlanGenerationJobRepository;

final class EloquentPlanGenerationJobRepository implements PlanGenerationJobRepository
{
    public function findById(PlanGenerationJobId $id): ?PlanGenerationJob
    {
        $model = EloquentPlanGenerationJob::find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function findPending(): array
    {
        return EloquentPlanGenerationJob::where('status', JobStatus::Pending->value)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(PlanGenerationJob $job): void
    {
        EloquentPlanGenerationJob::updateOrCreate(
            ['id' => $job->id()->value],
            [
                'planification_id' => $job->planificationId()->value,
                'status'           => $job->status()->value,
                'retries'          => $job->retries(),
                'error_message'    => $job->errorMessage(),
                'results'          => $job->results(),
                'started_at'       => $job->startedAt(),
                'completed_at'     => $job->completedAt(),
                'created_at'       => $job->createdAt(),
                'updated_at'       => $job->updatedAt(),
            ]
        );
    }

    private function toDomain(EloquentPlanGenerationJob $m): PlanGenerationJob
    {
        return PlanGenerationJob::reconstitute(
            PlanGenerationJobId::fromString($m->id),
            PlanificationId::fromString($m->planification_id),
            JobStatus::from($m->status),
            (int)$m->retries,
            $m->error_message,
            $m->results,
            $m->started_at?->toImmutable(),
            $m->completed_at?->toImmutable(),
            $m->created_at->toImmutable(),
            $m->updated_at->toImmutable()
        );
    }
}
