<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StopController;
use App\Http\Controllers\RouteController;

Route::prefix('v1/stops')->group(function () {
    Route::post('/', [StopController::class, 'store']);
    Route::post('/{id}/approve', [StopController::class, 'approve']);
    Route::get('/nearby', [StopController::class, 'nearby']);
});

Route::prefix('v1/routes')->group(function () {
    Route::post('/', [RouteController::class, 'store']);
    Route::post('/{id}/publish', [RouteController::class, 'publish']);
    Route::get('/{id}/schedules', [RouteController::class, 'schedules']);
    Route::get('/snapshots/{id}', [RouteController::class, 'showSnapshot']);
});
