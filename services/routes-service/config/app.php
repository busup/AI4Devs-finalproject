<?php

return [
    'name' => env('APP_NAME', 'RoutesService'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Routing\RoutingServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];
