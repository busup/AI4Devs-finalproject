<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\PlanGeneration\ProcessPlanGenerationJob;

use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\ValueObject\PlanGenerationJobId;
use PlanificationManagement\Domain\Repository\{
    ExpeditionRepository, PlanificationRepository, PlanGenerationJobRepository, ServiceRepository
};
use PlanificationManagement\Domain\Service\ServiceMaterializer;
use PlanificationManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class ProcessPlanGenerationJobHandler
{
    public function __construct(
        private PlanGenerationJobRepository $jobs,
        private PlanificationRepository     $planifications,
        private ExpeditionRepository        $expeditions,
        private ServiceRepository           $services,
        private ServiceMaterializer         $materializer,
        private OutboxEventStore            $outbox,
    ) {}

    public function handle(ProcessPlanGenerationJobCommand $command): void
    {
        $jobId = PlanGenerationJobId::fromString($command->jobId);
        $job   = $this->jobs->findById($jobId) ?? throw new \DomainException("Job not found.");

        $job->start();
        $this->jobs->save($job);

        try {
            $planification = $this->planifications->findById($job->planificationId())
                ?? throw new \DomainException("Planification not found.");

            $expedition = $this->expeditions->findById($planification->expeditionId())
                ?? throw new \DomainException("Expedition not found.");

            $generatedServices = $this->materializer->materialize($planification, $expedition);

            $events = [];
            foreach ($generatedServices as $svc) {
                $events = array_merge($events, $svc->pullDomainEvents());
            }

            DB::transaction(function () use ($job, $generatedServices, $events) {
                // Bulk save services and their stops (handled by repo implementation)
                $this->services->saveMany($generatedServices);

                // Publish ServiceCreated events to Outbox
                $this->outbox->store($events);

                // Complete Job
                $job->complete(['generated_count' => count($generatedServices)]);
                $this->jobs->save($job);
            });

        } catch (\Throwable $e) {
            $job->fail($e->getMessage());
            DB::transaction(fn() => $this->jobs->save($job));
            throw $e;
        }
    }
}
