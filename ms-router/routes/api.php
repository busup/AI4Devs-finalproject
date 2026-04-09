<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteTerminalStopsController;
use App\Http\Controllers\SearchRoutesController;
use App\Http\Controllers\StopController;

Route::prefix('v1')->group(function () {
    Route::post('/search/routes', [SearchRoutesController::class, 'search']);
});

Route::prefix('v1/stops')->group(function () {
    Route::get('/route-terminals', [RouteTerminalStopsController::class, 'index']);
    Route::post('/', [StopController::class, 'store']);
    Route::post('/{id}/approve', [StopController::class, 'approve']);
    Route::get('/nearby', [StopController::class, 'nearby']);
});

Route::prefix('v1/routes')->group(function () {
    Route::post('/', [RouteController::class, 'store']);
    Route::post('/{id}/publish', [RouteController::class, 'publish']);
    Route::get('/snapshots/{id}', [RouteController::class, 'showSnapshot']);
    Route::get('/{id}/schedules', [RouteController::class, 'schedules']);
    Route::get('/{id}', [RouteController::class, 'show']);
});
