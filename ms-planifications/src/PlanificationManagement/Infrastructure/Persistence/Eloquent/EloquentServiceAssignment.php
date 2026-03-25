<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentServiceAssignment extends Model
{
    protected $table      = 'service_assignments';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'service_id', 'vehicle_logical_id', 'driver_logical_id',
        'assignment_status', 'provider', 'decision_reason',
        'assigned_at', 'created_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at'  => 'datetime',
    ];
}
