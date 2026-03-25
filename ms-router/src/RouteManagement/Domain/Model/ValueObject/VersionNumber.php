<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

final readonly class VersionNumber
{
    public function __construct(public readonly int $value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException("VersionNumber must be >= 1, got {$value}.");
        }
    }

    public function next(): self
    {
        return new self($this->value + 1);
    }
}
