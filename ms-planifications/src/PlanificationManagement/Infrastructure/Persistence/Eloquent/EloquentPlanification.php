<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentPlanification extends Model
{
    protected $table      = 'planifications';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'expedition_id', 'date_from', 'date_until',
        'exceptions', 'non_working_days', 'status',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'date_from'        => 'date',
        'date_until'       => 'date',
        'exceptions'       => 'array',
        'non_working_days' => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];
}
