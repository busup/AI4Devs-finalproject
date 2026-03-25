<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\ActivatePlanification;

use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\ValueObject\PlanificationId;
use PlanificationManagement\Domain\Repository\PlanificationRepository;

final readonly class ActivatePlanificationHandler
{
    public function __construct(
        private PlanificationRepository $planifications,
    ) {}

    public function handle(ActivatePlanificationCommand $command): void
    {
        $id = PlanificationId::fromString($command->planificationId);
        $planification = $this->planifications->findById($id)
            ?? throw new \DomainException("Planification {$command->planificationId} not found.");

        $planification->activate();

        DB::transaction(function () use ($planification) {
            $this->planifications->save($planification);
        });
    }
}
