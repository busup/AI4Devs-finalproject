<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

final readonly class SequenceOrder
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("SequenceOrder must be >= 0, got {$value}.");
        }
    }
}
