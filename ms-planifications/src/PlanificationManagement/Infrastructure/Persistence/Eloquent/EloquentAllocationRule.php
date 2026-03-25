<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentAllocationRule extends Model
{
    protected $table      = 'allocation_rules';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'expedition_id', 'rule_type', 'parameters',
        'priority', 'active',
    ];

    protected $casts = [
        'parameters' => 'array',
        'active'     => 'boolean',
    ];
}
