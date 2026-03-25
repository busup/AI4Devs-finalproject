<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Event;

use PlanificationManagement\Domain\Model\ValueObject\{PlanificationId, ServiceId};

final readonly class ServiceCreated
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly ServiceId          $serviceId,
        public readonly PlanificationId    $planificationId,
        public readonly \DateTimeImmutable $serviceDate,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'ServiceCreated'; }

    public function aggregateType(): string { return 'Service'; }

    public function aggregateId(): string { return $this->serviceId->value; }

    public function toPayload(): array
    {
        return [
            'service_id'       => $this->serviceId->value,
            'planification_id' => $this->planificationId->value,
            'service_date'     => $this->serviceDate->format('Y-m-d'),
            'occurred_at'      => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
