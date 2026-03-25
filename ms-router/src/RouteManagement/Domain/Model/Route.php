<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model;

use RouteManagement\Domain\Model\ValueObject\{RouteId, RouteSnapshotId, RouteStatus};

final class Route
{
    private function __construct(
        private readonly RouteId        $id,
        private string                  $name,
        private RouteStatus             $status,
        private ?RouteSnapshotId        $currentSnapshotId,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable      $updatedAt,
    ) {}

    public static function create(RouteId $id, string $name): self
    {
        $now = new \DateTimeImmutable();
        return new self($id, $name, RouteStatus::Draft, null, $now, $now);
    }

    public static function reconstitute(
        RouteId             $id,
        string              $name,
        RouteStatus         $status,
        ?RouteSnapshotId    $currentSnapshotId,
        \DateTimeImmutable  $createdAt,
        \DateTimeImmutable  $updatedAt,
    ): self {
        return new self($id, $name, $status, $currentSnapshotId, $createdAt, $updatedAt);
    }

    public function publishSnapshot(RouteSnapshotId $snapshotId): void
    {
        $this->currentSnapshotId = $snapshotId;
        $this->status = RouteStatus::Approved;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function archive(): void
    {
        if ($this->status === RouteStatus::Archived) {
            return;
        }
        $this->status = RouteStatus::Archived;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isPublished(): bool     { return $this->status === RouteStatus::Approved; }

    public function id(): RouteId                     { return $this->id; }
    public function name(): string                    { return $this->name; }
    public function status(): RouteStatus             { return $this->status; }
    public function currentSnapshotId(): ?RouteSnapshotId { return $this->currentSnapshotId; }
    public function createdAt(): \DateTimeImmutable   { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable   { return $this->updatedAt; }
}
