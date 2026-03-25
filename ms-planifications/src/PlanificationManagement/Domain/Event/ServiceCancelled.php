<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Event;

use PlanificationManagement\Domain\Model\ValueObject\ServiceId;

final readonly class ServiceCancelled
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly ServiceId $serviceId,
        public readonly string    $reason,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'ServiceCancelled'; }

    public function aggregateType(): string { return 'Service'; }

    public function aggregateId(): string { return $this->serviceId->value; }

    public function toPayload(): array
    {
        return [
            'service_id'  => $this->serviceId->value,
            'reason'      => $this->reason,
            'occurred_at' => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
