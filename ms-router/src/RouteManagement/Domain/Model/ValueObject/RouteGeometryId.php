<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

final class RouteGeometryId extends UuidValueObject
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
