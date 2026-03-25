<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

final readonly class TimeOfDay
{
    /**
     * @param string $value Format HH:MM:SS
     */
    public function __construct(public readonly string $value)
    {
        if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/', $value)) {
            throw new \InvalidArgumentException("TimeOfDay must be in HH:MM:SS format, got {$value}.");
        }
    }

    public function addSeconds(int $seconds): self
    {
        $baseDate = \DateTimeImmutable::createFromFormat('H:i:s', $this->value);
        $interval = new \DateInterval("PT" . abs($seconds) . "S");

        $newDate = $seconds >= 0 ? $baseDate->add($interval) : $baseDate->sub($interval);

        return new self($newDate->format('H:i:s'));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
