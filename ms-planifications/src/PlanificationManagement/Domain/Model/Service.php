<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Event\{ServiceCancelled, ServiceCreated, ServiceUpdated};
use PlanificationManagement\Domain\Model\ValueObject\{
    PlanificationId, RouteSnapshotRefId, ServiceId, ServiceStatus, TimeOfDay
};

final class Service
{
    private array $domainEvents = [];
    /** @var ServiceStop[] */
    private array $stops = [];
    /** @var ServiceAssignment[] */
    private array $assignments = [];

    private function __construct(
        private readonly ServiceId          $id,
        private readonly PlanificationId    $planificationId,
        private readonly RouteSnapshotRefId $routeSnapshotRefId,
        private readonly \DateTimeImmutable $serviceDate,
        private readonly TimeOfDay          $departureTime,
        private readonly int                $capacity,
        private ServiceStatus               $status,
        private ?string                     $cancellationReason,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable          $updatedAt,
    ) {}

    public static function generate(
        ServiceId          $id,
        PlanificationId    $planificationId,
        RouteSnapshotRefId $routeSnapshotRefId,
        \DateTimeImmutable $serviceDate,
        TimeOfDay          $departureTime,
        int                $capacity,
    ): self {
        $now = new \DateTimeImmutable();
        $self = new self(
            $id, $planificationId, $routeSnapshotRefId,
            $serviceDate, $departureTime, $capacity,
            ServiceStatus::Scheduled, null,
            $now, $now,
        );

        $self->domainEvents[] = new ServiceCreated(
            $id, $planificationId, $serviceDate,
        );

        return $self;
    }

    public static function reconstitute(
        ServiceId          $id,
        PlanificationId    $planificationId,
        RouteSnapshotRefId $routeSnapshotRefId,
        \DateTimeImmutable $serviceDate,
        TimeOfDay          $departureTime,
        int                $capacity,
        ServiceStatus      $status,
        ?string            $cancellationReason,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        array              $stops = [],
        array              $assignments = [],
    ): self {
        $self = new self(
            $id, $planificationId, $routeSnapshotRefId,
            $serviceDate, $departureTime, $capacity,
            $status, $cancellationReason,
            $createdAt, $updatedAt,
        );
        $self->stops       = $stops;
        $self->assignments = $assignments;
        return $self;
    }

    public function updateStatus(ServiceStatus $newStatus, ?string $reason = null): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->updatedAt = new \DateTimeImmutable();

        if ($newStatus === ServiceStatus::Cancelled) {
            $this->cancellationReason = $reason ?? 'Unknown';
            $this->domainEvents[] = new ServiceCancelled($this->id, $this->cancellationReason);
        } else {
            $this->domainEvents[] = new ServiceUpdated($this->id, $oldStatus, $newStatus);
        }
    }

    public function confirm(): void { $this->updateStatus(ServiceStatus::Confirmed); }
    public function start(): void   { $this->updateStatus(ServiceStatus::Running); }
    public function complete(): void{ $this->updateStatus(ServiceStatus::Completed); }
    public function cancel(string $reason): void { $this->updateStatus(ServiceStatus::Cancelled, $reason); }

    public function isOperational(): bool
    {
        return !in_array($this->status, [ServiceStatus::Cancelled], true);
    }

    public function addStop(ServiceStop $stop): void {
        $this->stops[] = $stop;
    }

    public function addAssignment(ServiceAssignment $assignment): void {
        $this->assignments[] = $assignment;
    }

    public function id(): ServiceId                  { return $this->id; }
    public function planificationId(): PlanificationId { return $this->planificationId; }
    public function routeSnapshotRefId(): RouteSnapshotRefId { return $this->routeSnapshotRefId; }
    public function serviceDate(): \DateTimeImmutable  { return $this->serviceDate; }
    public function departureTime(): TimeOfDay         { return $this->departureTime; }
    public function capacity(): int                    { return $this->capacity; }
    public function status(): ServiceStatus            { return $this->status; }
    public function cancellationReason(): ?string      { return $this->cancellationReason; }
    public function createdAt(): \DateTimeImmutable    { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable    { return $this->updatedAt; }

    /** @return ServiceStop[] */
    public function stops(): array { return $this->stops; }
    /** @return ServiceAssignment[] */
    public function assignments(): array { return $this->assignments; }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
