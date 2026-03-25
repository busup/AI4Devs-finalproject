<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EloquentService extends Model
{
    protected $table      = 'services';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'planification_id', 'route_snapshot_ref_id',
        'service_date', 'departure_time', 'capacity',
        'status', 'cancellation_reason', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'service_date' => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function stops(): HasMany
    {
        return $this->hasMany(EloquentServiceStop::class, 'service_id', 'id')
                    ->orderBy('sequence_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EloquentServiceAssignment::class, 'service_id', 'id');
    }
}
