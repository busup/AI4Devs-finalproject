<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentOutboxEvent extends Model
{
    protected $table      = 'outbox_events';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'event_type', 'aggregate_type', 'aggregate_id',
        'payload', 'published', 'published_at', 'created_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'published'    => 'boolean',
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
    ];
}
