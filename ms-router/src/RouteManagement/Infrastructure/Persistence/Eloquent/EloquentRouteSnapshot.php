<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EloquentRouteSnapshot extends Model
{
    protected $table      = 'route_snapshots';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'route_id', 'version_number',
        'total_distance_m', 'estimated_duration_s',
        'valid_from', 'valid_until', 'published_at', 'created_at',
    ];

    protected $casts = [
        'valid_from'    => 'date',
        'valid_until'   => 'date',
        'published_at'  => 'datetime',
        'created_at'    => 'datetime',
    ];

    public function stops(): HasMany
    {
        return $this->hasMany(EloquentRouteStop::class, 'route_snapshot_id', 'id')
                    ->orderBy('sequence_order');
    }

    public function geometries(): HasMany
    {
        return $this->hasMany(EloquentRouteGeometry::class, 'route_snapshot_id', 'id');
    }
}
