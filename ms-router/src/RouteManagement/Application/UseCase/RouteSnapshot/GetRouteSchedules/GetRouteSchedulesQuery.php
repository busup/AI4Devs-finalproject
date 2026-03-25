<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSchedules;

final readonly class GetRouteSchedulesQuery
{
    public function __construct(
        public readonly string  $routeId,
        public readonly ?string $date = null,
    ) {}
}
