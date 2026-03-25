<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RouteManagement\Application\UseCase\Stop\CreateStop\{CreateStopCommand, CreateStopHandler};
use RouteManagement\Application\UseCase\Stop\ApproveStop\{ApproveStopCommand, ApproveStopHandler};
use RouteManagement\Application\UseCase\Stop\FindNearbyStops\{FindNearbyStopsQuery, FindNearbyStopsHandler};

final class StopController
{
    public function store(Request $request, CreateStopHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'address'       => 'nullable|string',
            'lat'           => 'required|numeric',
            'lon'           => 'required|numeric',
            'timezone'      => 'required|string',
            'is_accessible' => 'boolean',
            'metadata'      => 'nullable|array',
        ]);

        $command = new CreateStopCommand(
            $validated['name'],
            (float)$validated['lat'],
            (float)$validated['lon'],
            $validated['timezone'],
            $validated['address'] ?? null,
            $validated['is_accessible'] ?? false,
            $validated['metadata'] ?? null,
        );

        $id = $handler->handle($command);

        return response()->json(['id' => $id], 201);
    }

    public function approve(string $id, ApproveStopHandler $handler): JsonResponse
    {
        $handler->handle(new ApproveStopCommand($id));

        return response()->json(['status' => 'approved']);
    }

    public function nearby(Request $request, FindNearbyStopsHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'lat'           => 'required|numeric',
            'lon'           => 'required|numeric',
            'radius_meters' => 'numeric',
        ]);

        $query = new FindNearbyStopsQuery(
            (float)$validated['lat'],
            (float)$validated['lon'],
            (float)($validated['radius_meters'] ?? 2000.0)
        );

        return response()->json($handler->handle($query));
    }
}
