<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

final readonly class Timezone
{
    public function __construct(public readonly string $value)
    {
        if (!in_array($value, \DateTimeZone::listIdentifiers(), true)) {
            throw new \InvalidArgumentException("'{$value}' is not a valid timezone identifier.");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
