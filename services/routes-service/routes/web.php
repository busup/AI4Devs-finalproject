<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['service' => 'routes', 'message' => 'Use /api/v1/routes']);
});
