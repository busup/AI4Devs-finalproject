<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentPlanGenerationJob extends Model
{
    protected $table      = 'plan_generation_jobs';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'planification_id', 'status', 'retries',
        'error_message', 'results', 'started_at', 'completed_at',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'results'      => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}
