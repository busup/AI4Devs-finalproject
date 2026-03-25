<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

abstract class UuidValueObject
{
    public function __construct(public readonly string $value)
    {
        if (!preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $value)) {
            throw new \InvalidArgumentException("'$value' is not a valid UUID.");
        }
    }

    public function equals(static $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
