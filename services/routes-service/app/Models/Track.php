<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    protected $table = 'tracks';

    protected $fillable = [
        'route_id',
        'time',
        'polyline_raw',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
