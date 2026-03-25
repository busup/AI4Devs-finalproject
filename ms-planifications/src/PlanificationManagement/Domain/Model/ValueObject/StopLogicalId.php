<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

/** Logical reference to ms-router */
final class StopLogicalId extends UuidValueObject
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
