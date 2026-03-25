<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Event;

use PlanificationManagement\Domain\Model\ValueObject\{ServiceId, ServiceStatus};

final readonly class ServiceUpdated
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly ServiceId     $serviceId,
        public readonly ServiceStatus $previousStatus,
        public readonly ServiceStatus $newStatus,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'ServiceUpdated'; }

    public function aggregateType(): string { return 'Service'; }

    public function aggregateId(): string { return $this->serviceId->value; }

    public function toPayload(): array
    {
        return [
            'service_id'      => $this->serviceId->value,
            'previous_status' => $this->previousStatus->value,
            'new_status'      => $this->newStatus->value,
            'occurred_at'     => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
