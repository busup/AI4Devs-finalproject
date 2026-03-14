<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $table = 'routes';

    protected $fillable = [
        'title',
        'invitation_code',
        'province_id',
        'primary_site_id',
        'status',
        'polyline_raw',
        'start_timestamp',
        'end_timestamp',
    ];

    protected $casts = [
        'start_timestamp' => 'datetime',
        'end_timestamp' => 'datetime',
    ];

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class, 'route_id');
    }

    public function routeSchedules(): HasMany
    {
        return $this->hasMany(RouteSchedule::class, 'route_id');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'route_id');
    }
}
