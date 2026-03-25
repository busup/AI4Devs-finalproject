<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Event;

use PlanificationManagement\Domain\Model\ValueObject\{DriverLogicalId, ServiceId, VehicleLogicalId};

final readonly class ServiceAssigned
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly ServiceId         $serviceId,
        public readonly ?VehicleLogicalId $vehicleLogicalId,
        public readonly ?DriverLogicalId  $driverLogicalId,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function name(): string { return 'ServiceAssigned'; }

    public function aggregateType(): string { return 'Service'; }

    public function aggregateId(): string { return $this->serviceId->value; }

    public function toPayload(): array
    {
        return [
            'service_id'         => $this->serviceId->value,
            'vehicle_logical_id' => $this->vehicleLogicalId?->value,
            'driver_logical_id'  => $this->driverLogicalId?->value,
            'occurred_at'        => $this->occurredAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
