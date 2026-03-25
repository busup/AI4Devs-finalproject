<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\PublishRoute;

final readonly class PublishRouteCommand
{
    /**
     * @param  string   $routeId      UUID of the Route to publish
     * @param  string[] $stopIds      Ordered list of Stop UUIDs forming the snapshot
     * @param  string|null $validFrom  ISO date (YYYY-MM-DD) or null
     * @param  string|null $validUntil ISO date (YYYY-MM-DD) or null
     */
    public function __construct(
        public readonly string  $routeId,
        public readonly array   $stopIds,
        public readonly ?string $validFrom  = null,
        public readonly ?string $validUntil = null,
    ) {}
}
