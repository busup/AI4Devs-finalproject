<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Service\UpdateServiceStatus;

use Illuminate\Support\Facades\DB;
use PlanificationManagement\Domain\Model\ValueObject\{ServiceId, ServiceStatus};
use PlanificationManagement\Domain\Repository\ServiceRepository;
use PlanificationManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class UpdateServiceStatusHandler
{
    public function __construct(
        private ServiceRepository $services,
        private OutboxEventStore  $outbox,
    ) {}

    public function handle(UpdateServiceStatusCommand $command): void
    {
        $id      = ServiceId::fromString($command->serviceId);
        $service = $this->services->findById($id)
            ?? throw new \DomainException("Service {$command->serviceId} not found.");

        $statusEnum = ServiceStatus::from($command->status);

        switch ($statusEnum) {
            case ServiceStatus::Confirmed:
                $service->confirm();
                break;
            case ServiceStatus::Running:
                $service->start();
                break;
            case ServiceStatus::Completed:
                $service->complete();
                break;
            case ServiceStatus::Cancelled:
                $service->cancel($command->reason ?? 'No reason provided');
                break;
            default:
                throw new \InvalidArgumentException("Invalid status transition to {$command->status}");
        }

        DB::transaction(function () use ($service) {
            $this->services->save($service);
            $this->outbox->store($service->pullDomainEvents()); // ServiceUpdated or ServiceCancelled
        });
    }
}
