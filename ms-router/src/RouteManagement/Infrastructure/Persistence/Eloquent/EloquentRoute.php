<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EloquentRoute extends Model
{
    protected $table      = 'routes';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id', 'name', 'status', 'current_snapshot_id',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(EloquentRouteSnapshot::class, 'route_id', 'id');
    }
}
