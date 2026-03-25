<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EloquentRouteGeometry extends Model
{
    protected $table      = 'route_geometries';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'route_snapshot_id', 'geometry_type', 'format',
        'provider', 'segment_index', 'accuracy_m', 'content', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
