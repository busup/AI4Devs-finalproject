<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

final class ExpeditionId extends UuidValueObject
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
