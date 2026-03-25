<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\CreatePlanification;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\{Planification, PlanGenerationJob};
use PlanificationManagement\Domain\Model\ValueObject\{
    DateCollection, ExpeditionId, PlanGenerationJobId, PlanificationId
};
use PlanificationManagement\Domain\Repository\{
    ExpeditionRepository, PlanificationRepository, PlanGenerationJobRepository
};

final readonly class CreatePlanificationHandler
{
    public function __construct(
        private ExpeditionRepository        $expeditions,
        private PlanificationRepository     $planifications,
        private PlanGenerationJobRepository $jobs,
    ) {}

    public function handle(CreatePlanificationCommand $command): string
    {
        $expeditionId = ExpeditionId::fromString($command->expeditionId);
        $expedition   = $this->expeditions->findById($expeditionId)
            ?? throw new \DomainException("Expedition {$command->expeditionId} not found.");

        $planificationId = PlanificationId::fromString(Str::uuid()->toString());
        $jobId           = PlanGenerationJobId::fromString(Str::uuid()->toString());

        $planification = Planification::create(
            $planificationId,
            $expedition->id(),
            new \DateTimeImmutable($command->dateFrom),
            new \DateTimeImmutable($command->dateUntil),
            new DateCollection($command->exceptions),
            new DateCollection($command->nonWorkingDays),
        );

        // Al crear la planificación, generamos automáticamente el Job pendiente
        $job = PlanGenerationJob::create($jobId, $planificationId);

        DB::transaction(function () use ($planification, $job) {
            $this->planifications->save($planification);
            $this->jobs->save($job);
        });

        return $planificationId->value;
    }
}
