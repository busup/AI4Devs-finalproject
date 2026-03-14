<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    protected $table = 'route_stops';

    protected $fillable = [
        'route_id',
        'stop_type_id',
        'lat',
        'lng',
        'title',
        'known_title',
        'type_stop',
        'start_timestamp',
        'end_timestamp',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
