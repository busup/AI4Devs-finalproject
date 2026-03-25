<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model;

use RouteManagement\Domain\Event\StopCreated;
use RouteManagement\Domain\Model\ValueObject\{ApprovalStatus, Coordinates, StopId, Timezone};

final class Stop
{
    private array $domainEvents = [];

    private function __construct(
        private readonly StopId        $id,
        private string                 $name,
        private ?string                $address,
        private Coordinates            $location,
        private Timezone               $timezone,
        private bool                   $isAccessible,
        private ApprovalStatus         $approvalStatus,
        private ?array                 $metadata,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable     $updatedAt,
        private ?\DateTimeImmutable    $deletedAt = null,
    ) {}

    public static function create(
        StopId      $id,
        string      $name,
        ?string     $address,
        Coordinates $location,
        Timezone    $timezone,
        bool        $isAccessible = false,
        ?array      $metadata = null,
    ): self {
        $now = new \DateTimeImmutable();
        return new self(
            $id, $name, $address, $location, $timezone,
            $isAccessible, ApprovalStatus::Pending, $metadata, $now, $now,
        );
    }

    /** Reconstitution from persistence — no domain events emitted. */
    public static function reconstitute(
        StopId              $id,
        string              $name,
        ?string             $address,
        Coordinates         $location,
        Timezone            $timezone,
        bool                $isAccessible,
        ApprovalStatus      $approvalStatus,
        ?array              $metadata,
        \DateTimeImmutable  $createdAt,
        \DateTimeImmutable  $updatedAt,
        ?\DateTimeImmutable $deletedAt,
    ): self {
        return new self(
            $id, $name, $address, $location, $timezone,
            $isAccessible, $approvalStatus, $metadata, $createdAt, $updatedAt, $deletedAt,
        );
    }

    public function approve(): void
    {
        if ($this->approvalStatus === ApprovalStatus::Approved) {
            return;
        }
        $this->approvalStatus = ApprovalStatus::Approved;
        $this->updatedAt = new \DateTimeImmutable();
        $this->domainEvents[] = new StopCreated($this->id, $this->location);
    }

    public function reject(): void
    {
        $this->approvalStatus = ApprovalStatus::Rejected;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool  { return $this->deletedAt !== null; }
    public function isApproved(): bool { return $this->approvalStatus === ApprovalStatus::Approved; }

    public function id(): StopId                     { return $this->id; }
    public function name(): string                   { return $this->name; }
    public function address(): ?string               { return $this->address; }
    public function location(): Coordinates          { return $this->location; }
    public function timezone(): Timezone             { return $this->timezone; }
    public function isAccessible(): bool             { return $this->isAccessible; }
    public function approvalStatus(): ApprovalStatus { return $this->approvalStatus; }
    public function metadata(): ?array               { return $this->metadata; }
    public function createdAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable  { return $this->updatedAt; }
    public function deletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
