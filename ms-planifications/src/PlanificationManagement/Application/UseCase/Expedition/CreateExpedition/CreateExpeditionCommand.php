<?php

declare(strict_types=1);

namespace PlanificationManagement\Application\UseCase\Expedition\CreateExpedition;

final readonly class CreateExpeditionCommand
{
    /**
     * @param string $name
     * @param string $routeSnapshotRefId
     * @param int    $daysOfWeekBitmask
     * @param string $baseTime
     * @param array  $metadata
     * @param array  $stops [ ['stopId' => string, 'sequence' => int, 'offsetS' => int, 'pickup' => bool, 'dropoff' => bool], ... ]
     * @param array  $capacityRules [ ['maxSeats' => int, '...'], ... ]
     */
    public function __construct(
        public readonly string $name,
        public readonly string $routeSnapshotRefId,
        public readonly int    $daysOfWeekBitmask,
        public readonly string $baseTime,
        public readonly ?array $metadata = null,
        public readonly array  $stops = [],
        public readonly array  $capacityRules = [],
    ) {}
}
