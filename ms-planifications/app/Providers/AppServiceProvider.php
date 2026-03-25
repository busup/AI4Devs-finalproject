<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PlanificationManagement\Domain\Repository\{
    ExpeditionRepository, PlanGenerationJobRepository, PlanificationRepository, ServiceRepository
};
use PlanificationManagement\Domain\Service\RouteSnapshotValidator;
use PlanificationManagement\Infrastructure\Http\HttpRouteSnapshotValidator;
use PlanificationManagement\Infrastructure\Persistence\Eloquent\{
    EloquentExpeditionRepository, EloquentPlanGenerationJobRepository,
    EloquentPlanificationRepository, EloquentServiceRepository
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind DDD Interfaces to Eloquent and HTTP Implementations
        $this->app->bind(ExpeditionRepository::class, EloquentExpeditionRepository::class);
        $this->app->bind(PlanificationRepository::class, EloquentPlanificationRepository::class);
        $this->app->bind(PlanGenerationJobRepository::class, EloquentPlanGenerationJobRepository::class);
        $this->app->bind(ServiceRepository::class, EloquentServiceRepository::class);
        
        $this->app->bind(RouteSnapshotValidator::class, HttpRouteSnapshotValidator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
