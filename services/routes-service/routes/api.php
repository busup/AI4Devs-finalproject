<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\RoutesController;

/*
| API routes - Routes Service (Lines, Stops, Schedules).
| Gateway forwards /api/v1/routes/* here.
*/
Route::prefix('api/v1/routes')->group(function () {
    Route::get('health', [HealthController::class, 'index']);
    Route::get('/', [RoutesController::class, 'index']);
});
