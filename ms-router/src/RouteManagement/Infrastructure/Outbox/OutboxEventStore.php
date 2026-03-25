<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Outbox;

use Illuminate\Support\Str;
use RouteManagement\Infrastructure\Persistence\Eloquent\EloquentOutboxEvent;

final class OutboxEventStore
{
    /**
     * @param object[] $domainEvents Array of Domain Event objects with toPayload() method
     */
    public function store(array $domainEvents): void
    {
        foreach ($domainEvents as $event) {
            EloquentOutboxEvent::create([
                'id'             => Str::uuid()->toString(),
                'event_type'     => $event->name(),
                'aggregate_type' => $event->aggregateType(),
                'aggregate_id'   => $event->aggregateId(),
                'payload'        => $event->toPayload(),
                'published'      => false,
                'published_at'   => null,
                'created_at'     => $event->occurredAt,
            ]);
        }
    }
}
