<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EloquentExpedition extends Model
{
    protected $table      = 'expeditions';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'name', 'route_snapshot_ref_id', 'days_of_week',
        'base_time', 'status', 'metadata', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function stops(): HasMany
    {
        return $this->hasMany(EloquentExpeditionStop::class, 'expedition_id', 'id')
                    ->orderBy('sequence_order');
    }

    public function capacityRules(): HasMany
    {
        return $this->hasMany(EloquentCapacityRule::class, 'expedition_id', 'id');
    }

    public function allocationRules(): HasMany
    {
        return $this->hasMany(EloquentAllocationRule::class, 'expedition_id', 'id');
    }
}
