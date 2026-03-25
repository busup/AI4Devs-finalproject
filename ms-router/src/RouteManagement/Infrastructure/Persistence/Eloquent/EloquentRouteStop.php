<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentRouteStop extends Model
{
    protected $table      = 'route_stops';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'route_snapshot_id', 'stop_id', 'sequence_order',
        'dwell_time_s', 'alias', 'pickup_allowed', 'dropoff_allowed', 'active',
    ];

    protected $casts = [
        'pickup_allowed'  => 'boolean',
        'dropoff_allowed' => 'boolean',
        'active'          => 'boolean',
    ];
}
