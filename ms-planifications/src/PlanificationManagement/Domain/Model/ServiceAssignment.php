<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{
    AssignmentStatus, DriverLogicalId, ServiceAssignmentId, ServiceId, VehicleLogicalId
};

final class ServiceAssignment
{
    public function __construct(
        private readonly ServiceAssignmentId $id,
        private readonly ServiceId           $serviceId,
        private readonly ?VehicleLogicalId   $vehicleLogicalId,
        private readonly ?DriverLogicalId    $driverLogicalId,
        private AssignmentStatus             $assignmentStatus,
        private ?string                      $provider,
        private ?string                      $decisionReason,
        private readonly ?\DateTimeImmutable $assignedAt,
        private readonly \DateTimeImmutable  $createdAt,
    ) {}

    public static function create(
        ServiceAssignmentId $id,
        ServiceId           $serviceId,
        ?VehicleLogicalId   $vehicleLogicalId,
        ?DriverLogicalId    $driverLogicalId,
        ?string             $provider,
    ): self {
        return new self(
            $id, $serviceId, $vehicleLogicalId, $driverLogicalId,
            AssignmentStatus::Assigned, $provider, null,
            new \DateTimeImmutable(), new \DateTimeImmutable(),
        );
    }

    public function reject(string $reason): void
    {
        $this->assignmentStatus = AssignmentStatus::Rejected;
        $this->decisionReason   = $reason;
    }

    public function cancel(): void
    {
        $this->assignmentStatus = AssignmentStatus::Cancelled;
    }

    public function id(): ServiceAssignmentId          { return $this->id; }
    public function serviceId(): ServiceId             { return $this->serviceId; }
    public function vehicleLogicalId(): ?VehicleLogicalId { return $this->vehicleLogicalId; }
    public function driverLogicalId(): ?DriverLogicalId   { return $this->driverLogicalId; }
    public function assignmentStatus(): AssignmentStatus  { return $this->assignmentStatus; }
    public function provider(): ?string                { return $this->provider; }
    public function decisionReason(): ?string          { return $this->decisionReason; }
    public function assignedAt(): ?\DateTimeImmutable  { return $this->assignedAt; }
    public function createdAt(): \DateTimeImmutable    { return $this->createdAt; }
}
