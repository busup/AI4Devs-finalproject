<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use RouteManagement\Application\UseCase\Route\ListRouteTerminalStops\ListRouteTerminalStopsHandler;

final class RouteTerminalStopsController
{
    public function index(ListRouteTerminalStopsHandler $handler): JsonResponse
    {
        return response()->json($handler->handle());
    }
}
