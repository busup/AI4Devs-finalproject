<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{
    DaysOfWeek, ExpeditionId, ExpeditionStatus, RouteSnapshotRefId, TimeOfDay
};

final class Expedition
{
    /** @var ExpeditionStop[] */
    private array $stops = [];
    /** @var CapacityRule[] */
    private array $capacityRules = [];
    /** @var AllocationRule[] */
    private array $allocationRules = [];

    private function __construct(
        private readonly ExpeditionId       $id,
        private string                      $name,
        private readonly RouteSnapshotRefId $routeSnapshotRefId,
        private DaysOfWeek                  $daysOfWeek,
        private TimeOfDay                   $baseTime,
        private ExpeditionStatus            $status,
        private ?array                      $metadata,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable          $updatedAt,
        private ?\DateTimeImmutable         $deletedAt = null,
    ) {}

    public static function create(
        ExpeditionId       $id,
        string             $name,
        RouteSnapshotRefId $routeSnapshotRefId,
        DaysOfWeek         $daysOfWeek,
        TimeOfDay          $baseTime,
        ?array             $metadata = null,
    ): self {
        $now = new \DateTimeImmutable();
        return new self(
            $id, $name, $routeSnapshotRefId, $daysOfWeek, $baseTime,
            ExpeditionStatus::Draft, $metadata, $now, $now,
        );
    }

    public static function reconstitute(
        ExpeditionId        $id,
        string              $name,
        RouteSnapshotRefId  $routeSnapshotRefId,
        DaysOfWeek          $daysOfWeek,
        TimeOfDay           $baseTime,
        ExpeditionStatus    $status,
        ?array              $metadata,
        \DateTimeImmutable  $createdAt,
        \DateTimeImmutable  $updatedAt,
        ?\DateTimeImmutable $deletedAt,
        array               $stops = [],
        array               $capacityRules = [],
        array               $allocationRules = [],
    ): self {
        $self = new self(
            $id, $name, $routeSnapshotRefId, $daysOfWeek, $baseTime,
            $status, $metadata, $createdAt, $updatedAt, $deletedAt,
        );
        $self->stops           = $stops;
        $self->capacityRules   = $capacityRules;
        $self->allocationRules = $allocationRules;
        return $self;
    }

    public function activate(): void
    {
        if ($this->status === ExpeditionStatus::Active) {
            return;
        }
        $this->status = ExpeditionStatus::Active;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->status = ExpeditionStatus::Archived;
        $this->updatedAt = clone new \DateTimeImmutable();
    }

    public function addStop(ExpeditionStop $stop): void {
        $this->stops[] = $stop;
    }

    public function addCapacityRule(CapacityRule $rule): void {
        $this->capacityRules[] = $rule;
    }

    public function addAllocationRule(AllocationRule $rule): void {
        $this->allocationRules[] = $rule;
    }

    public function maxSeats(): int
    {
        $max = 0;
        foreach ($this->capacityRules as $rule) {
            if ($rule->active() && $rule->maxSeats() > $max) {
                $max = $rule->maxSeats();
            }
        }
        return $max > 0 ? $max : 50; // default safe fallback
    }

    public function id(): ExpeditionId                 { return $this->id; }
    public function name(): string                     { return $this->name; }
    public function routeSnapshotRefId(): RouteSnapshotRefId { return $this->routeSnapshotRefId; }
    public function daysOfWeek(): DaysOfWeek           { return $this->daysOfWeek; }
    public function baseTime(): TimeOfDay              { return $this->baseTime; }
    public function status(): ExpeditionStatus         { return $this->status; }
    public function metadata(): ?array                 { return $this->metadata; }
    public function createdAt(): \DateTimeImmutable    { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable    { return $this->updatedAt; }
    public function deletedAt(): ?\DateTimeImmutable   { return $this->deletedAt; }

    /** @return ExpeditionStop[] */
    public function stops(): array { return $this->stops; }
    /** @return CapacityRule[] */
    public function capacityRules(): array { return $this->capacityRules; }
    /** @return AllocationRule[] */
    public function allocationRules(): array { return $this->allocationRules; }
}
