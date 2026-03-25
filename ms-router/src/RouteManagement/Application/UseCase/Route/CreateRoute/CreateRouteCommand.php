<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\CreateRoute;

final readonly class CreateRouteCommand
{
    public function __construct(
        public readonly string $name,
    ) {}
}
