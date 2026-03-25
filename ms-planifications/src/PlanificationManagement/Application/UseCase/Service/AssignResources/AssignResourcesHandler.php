<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Service\AssignResources;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Event\ServiceAssigned;
use PlanificationManagement\Domain\Model\ServiceAssignment;
use PlanificationManagement\Domain\Model\ValueObject\{
    DriverLogicalId, ServiceAssignmentId, ServiceId, VehicleLogicalId
};
use PlanificationManagement\Domain\Repository\ServiceRepository;
use PlanificationManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class AssignResourcesHandler
{
    public function __construct(
        private ServiceRepository $services,
        private OutboxEventStore  $outbox,
    ) {}

    public function handle(AssignResourcesCommand $command): string
    {
        $serviceId = ServiceId::fromString($command->serviceId);
        $service   = $this->services->findById($serviceId)
            ?? throw new \DomainException("Service {$command->serviceId} not found.");

        if (!$service->isOperational()) {
            throw new \DomainException("Cannot assign resources to cancelled service.");
        }

        $assignmentId = ServiceAssignmentId::fromString(Str::uuid()->toString());
        $vLogId       = $command->vehicleLogicalId ? VehicleLogicalId::fromString($command->vehicleLogicalId) : null;
        $dLogId       = $command->driverLogicalId ? DriverLogicalId::fromString($command->driverLogicalId) : null;

        $assignment = ServiceAssignment::create(
            $assignmentId, $serviceId, $vLogId, $dLogId, $command->provider,
        );

        $service->addAssignment($assignment);

        // This isn't strictly generated inside the Aggregate currently, so we emit manually
        $event = new ServiceAssigned($serviceId, $vLogId, $dLogId);

        DB::transaction(function () use ($service, $event) {
            $this->services->save($service);
            $this->outbox->store([$event]);
        });

        return $assignmentId->value;
    }
}
