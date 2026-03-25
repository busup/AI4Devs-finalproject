<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\CreateStop;

final readonly class CreateStopCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly float   $lat,
        public readonly float   $lon,
        public readonly string  $timezone,
        public readonly ?string $address      = null,
        public readonly bool    $isAccessible = false,
        public readonly ?array  $metadata     = null,
    ) {}
}
