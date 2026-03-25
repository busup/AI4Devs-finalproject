<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{JobStatus, PlanGenerationJobId, PlanificationId};

final class PlanGenerationJob
{
    private function __construct(
        private readonly PlanGenerationJobId $id,
        private readonly PlanificationId     $planificationId,
        private JobStatus                    $status,
        private int                          $retries,
        private ?string                      $errorMessage,
        private ?array                       $results,
        private ?\DateTimeImmutable          $startedAt,
        private ?\DateTimeImmutable          $completedAt,
        private readonly \DateTimeImmutable  $createdAt,
        private \DateTimeImmutable           $updatedAt,
    ) {}

    public static function create(PlanGenerationJobId $id, PlanificationId $planificationId): self
    {
        $now = new \DateTimeImmutable();
        return new self(
            $id, $planificationId, JobStatus::Pending, 0,
            null, null, null, null, $now, $now,
        );
    }

    public static function reconstitute(
        PlanGenerationJobId $id,
        PlanificationId     $planificationId,
        JobStatus           $status,
        int                 $retries,
        ?string             $errorMessage,
        ?array              $results,
        ?\DateTimeImmutable $startedAt,
        ?\DateTimeImmutable $completedAt,
        \DateTimeImmutable  $createdAt,
        \DateTimeImmutable  $updatedAt,
    ): self {
        return new self(
            $id, $planificationId, $status, $retries,
            $errorMessage, $results, $startedAt, $completedAt,
            $createdAt, $updatedAt,
        );
    }

    public function start(): void
    {
        $this->status    = JobStatus::Running;
        $this->startedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function complete(array $results): void
    {
        $this->status      = JobStatus::Completed;
        $this->results     = $results;
        $this->completedAt = new \DateTimeImmutable();
        $this->updatedAt   = clone $this->completedAt;
    }

    public function fail(string $error): void
    {
        $this->status       = JobStatus::Failed;
        $this->errorMessage = $error;
        $this->completedAt  = new \DateTimeImmutable();
        $this->updatedAt    = clone $this->completedAt;
        $this->retries++;
    }

    public function canRetry(): bool
    {
        return $this->status === JobStatus::Failed && $this->retries < 3;
    }

    public function id(): PlanGenerationJobId            { return $this->id; }
    public function planificationId(): PlanificationId   { return $this->planificationId; }
    public function status(): JobStatus                  { return $this->status; }
    public function retries(): int                       { return $this->retries; }
    public function errorMessage(): ?string              { return $this->errorMessage; }
    public function results(): ?array                    { return $this->results; }
    public function startedAt(): ?\DateTimeImmutable     { return $this->startedAt; }
    public function completedAt(): ?\DateTimeImmutable   { return $this->completedAt; }
    public function createdAt(): \DateTimeImmutable      { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable      { return $this->updatedAt; }
}
