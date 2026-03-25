<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\ApproveStop;

final readonly class ApproveStopCommand
{
    public function __construct(
        public readonly string $stopId,
    ) {}
}
