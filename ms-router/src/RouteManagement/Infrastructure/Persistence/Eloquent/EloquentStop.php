<?php

declare(strict_types=1);

namespace RouteManagement\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EloquentStop extends Model
{
    protected $table      = 'stops';
    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false; // we manage created_at/updated_at manually

    protected $fillable = [
        'id', 'name', 'address', 'lat', 'lon', 'location',
        'timezone', 'is_accessible', 'approval_status',
        'metadata', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'is_accessible' => 'boolean',
        'metadata'      => 'array',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];
}
