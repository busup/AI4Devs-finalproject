<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentServiceStop extends Model
{
    protected $table      = 'service_stops';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'service_id', 'stop_logical_id', 'sequence_order',
        'scheduled_time', 'active', 'pickup_allowed', 'dropoff_allowed',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'pickup_allowed'  => 'boolean',
        'dropoff_allowed' => 'boolean',
    ];
}
