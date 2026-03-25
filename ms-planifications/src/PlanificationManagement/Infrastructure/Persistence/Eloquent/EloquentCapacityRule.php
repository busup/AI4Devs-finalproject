<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentCapacityRule extends Model
{
    protected $table      = 'capacity_rules';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'expedition_id', 'max_seats', 'segment_rules',
        'client_restrictions', 'active',
    ];

    protected $casts = [
        'segment_rules'       => 'array',
        'client_restrictions' => 'array',
        'active'              => 'boolean',
    ];
}
