<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Route\CreateRoute;

use Illuminate\Support\Str;
use RouteManagement\Domain\Model\Route;
use RouteManagement\Domain\Model\ValueObject\RouteId;
use RouteManagement\Domain\Repository\RouteRepository;

final readonly class CreateRouteHandler
{
    public function __construct(
        private RouteRepository $routes,
    ) {}

    public function handle(CreateRouteCommand $command): string
    {
        $id    = RouteId::fromString(Str::uuid()->toString());
        $route = Route::create($id, $command->name);

        $this->routes->save($route);

        return $id->value;
    }
}
