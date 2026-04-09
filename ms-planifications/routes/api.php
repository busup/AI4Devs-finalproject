<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ExpeditionController, PlanificationController, ScheduleBySnapshotsController, ServiceController};

Route::prefix('v1/expeditions')->group(function () {
    Route::post('/', [ExpeditionController::class, 'store']);
    Route::post('/{id}/activate', [ExpeditionController::class, 'activate']);
    Route::get('/{id}', [ExpeditionController::class, 'show']);
});

Route::prefix('v1/planifications')->group(function () {
    Route::post('/', [PlanificationController::class, 'store']);
    Route::post('/{id}/activate', [PlanificationController::class, 'activate']);
    Route::post('/{id}/cancel', [PlanificationController::class, 'cancel']);
});

Route::prefix('v1/services')->group(function () {
    Route::patch('/{id}/status', [ServiceController::class, 'updateStatus']);
    Route::post('/{id}/assignments', [ServiceController::class, 'assignResources']);
});

Route::post('v1/schedules/by-snapshots', ScheduleBySnapshotsController::class);
