<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RouteManagement\Domain\Repository\{RouteRepository, RouteSnapshotRepository, StopRepository};
use RouteManagement\Infrastructure\Persistence\Eloquent\{
    EloquentRouteRepository, EloquentRouteSnapshotRepository, EloquentStopRepository
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind DDD Interfaces to Eloquent Implementations
        $this->app->bind(StopRepository::class, EloquentStopRepository::class);
        $this->app->bind(RouteRepository::class, EloquentRouteRepository::class);
        $this->app->bind(RouteSnapshotRepository::class, EloquentRouteSnapshotRepository::class);
        
        // OutboxEventStore no es interfaz, se puede inyectar automáticamente por el contenedor.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
