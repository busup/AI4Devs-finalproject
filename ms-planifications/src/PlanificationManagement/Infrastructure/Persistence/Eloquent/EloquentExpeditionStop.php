<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentExpeditionStop extends Model
{
    protected $table      = 'expedition_stops';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false; // no timestamps

    protected $fillable = [
        'id', 'expedition_id', 'stop_logical_id', 'sequence_order',
        'offset_seconds', 'active', 'pickup_allowed', 'dropoff_allowed',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'pickup_allowed'  => 'boolean',
        'dropoff_allowed' => 'boolean',
    ];
}
