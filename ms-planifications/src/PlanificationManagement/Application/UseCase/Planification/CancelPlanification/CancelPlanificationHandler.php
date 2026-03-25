<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Planification\CancelPlanification;

use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\ValueObject\PlanificationId;
use PlanificationManagement\Domain\Repository\PlanificationRepository;

final readonly class CancelPlanificationHandler
{
    public function __construct(
        private PlanificationRepository $planifications,
    ) {}

    // Note: cancelling a planification does NOT automatically cancel all generated future services.
    // That requires a separate asynchronous process or specialized command.
    public function handle(CancelPlanificationCommand $command): void
    {
        $id = PlanificationId::fromString($command->planificationId);
        $planification = $this->planifications->findById($id)
            ?? throw new \DomainException("Planification {$command->planificationId} not found.");

        $planification->cancel();

        DB::transaction(function () use ($planification) {
            $this->planifications->save($planification);
        });
    }
}
