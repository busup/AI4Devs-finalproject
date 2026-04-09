<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RouteManagement\Application\UseCase\Route\SearchRoutes\SearchRoutesHandler;

final class SearchRoutesController
{
    public function search(Request $request, SearchRoutesHandler $handler): JsonResponse
    {
        $request->validate([
            'origin' => 'nullable|array',
            'origin.latitude' => 'nullable|numeric',
            'origin.longitude' => 'nullable|numeric',
            'destination' => 'nullable|array',
            'destination.latitude' => 'nullable|numeric',
            'destination.longitude' => 'nullable|numeric',
            'date' => 'nullable|date_format:Y-m-d',
            'journeyType' => 'nullable|string|in:outbound,return,roundtrip',
        ]);

        return response()->json($handler->handle());
    }
}
