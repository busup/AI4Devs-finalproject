<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model;

use PlanificationManagement\Domain\Model\ValueObject\{
    DateCollection, ExpeditionId, PlanificationId, PlanificationStatus
};

final class Planification
{
    private function __construct(
        private readonly PlanificationId    $id,
        private readonly ExpeditionId       $expeditionId,
        private \DateTimeImmutable          $dateFrom,
        private \DateTimeImmutable          $dateUntil,
        private DateCollection              $exceptions,
        private DateCollection              $nonWorkingDays,
        private PlanificationStatus         $status,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable          $updatedAt,
    ) {}

    public static function create(
        PlanificationId    $id,
        ExpeditionId       $expeditionId,
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateUntil,
        DateCollection     $exceptions,
        DateCollection     $nonWorkingDays,
    ): self {
        if ($dateUntil < $dateFrom) {
            throw new \InvalidArgumentException('dateUntil cannot be before dateFrom.');
        }
        $now = new \DateTimeImmutable();
        return new self(
            $id, $expeditionId, $dateFrom, $dateUntil,
            $exceptions, $nonWorkingDays, PlanificationStatus::Draft,
            $now, $now,
        );
    }

    public static function reconstitute(
        PlanificationId    $id,
        ExpeditionId       $expeditionId,
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateUntil,
        DateCollection     $exceptions,
        DateCollection     $nonWorkingDays,
        PlanificationStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $id, $expeditionId, $dateFrom, $dateUntil,
            $exceptions, $nonWorkingDays, $status,
            $createdAt, $updatedAt,
        );
    }

    public function activate(): void
    {
        if ($this->status === PlanificationStatus::Active) {
            return;
        }
        $this->status = PlanificationStatus::Active;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function cancel(): void
    {
        $this->status = PlanificationStatus::Cancelled;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Determines if an expedition should spawn a Service on a given physical date.
     * Takes exceptions and non-working days into account.
     */
    public function shouldRunOn(\DateTimeImmutable $date): bool
    {
        if ($this->status !== PlanificationStatus::Active) {
            return false;
        }

        // Date is outside bounds
        if ($date < $this->dateFrom || $date > $this->dateUntil) {
            return false;
        }

        if ($this->exceptions->contains($date)) {
             return false;
        }

        if ($this->nonWorkingDays->contains($date)) {
             return false;
        }

        return true;
    }

    public function id(): PlanificationId                { return $this->id; }
    public function expeditionId(): ExpeditionId         { return $this->expeditionId; }
    public function dateFrom(): \DateTimeImmutable       { return $this->dateFrom; }
    public function dateUntil(): \DateTimeImmutable      { return $this->dateUntil; }
    public function exceptions(): DateCollection         { return $this->exceptions; }
    public function nonWorkingDays(): DateCollection     { return $this->nonWorkingDays; }
    public function status(): PlanificationStatus        { return $this->status; }
    public function createdAt(): \DateTimeImmutable      { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable      { return $this->updatedAt; }
}
