<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RouteManagement\Application\UseCase\Route\CreateRoute\{CreateRouteCommand, CreateRouteHandler};
use RouteManagement\Application\UseCase\Route\PublishRoute\{PublishRouteCommand, PublishRouteHandler};
use RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSchedules\{GetRouteSchedulesQuery, GetRouteSchedulesHandler};
use RouteManagement\Application\UseCase\RouteSnapshot\GetRouteSnapshot\{GetRouteSnapshotQuery, GetRouteSnapshotHandler};

final class RouteController
{
    public function store(Request $request, CreateRouteHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $id = $handler->handle(new CreateRouteCommand($validated['name']));

        return response()->json(['id' => $id], 201);
    }

    public function publish(Request $request, string $id, PublishRouteHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'stops'       => 'required|array|min:2',
            'stops.*'     => 'required|uuid',
            'valid_from'  => 'nullable|date_format:Y-m-d',
            'valid_until' => 'nullable|date_format:Y-m-d',
        ]);

        $snapshotId = $handler->handle(new PublishRouteCommand(
            $id,
            $validated['stops'],
            $validated['valid_from'] ?? null,
            $validated['valid_until'] ?? null,
        ));

        return response()->json(['snapshot_id' => $snapshotId]);
    }

    public function schedules(Request $request, string $id, GetRouteSchedulesHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $data = $handler->handle(new GetRouteSchedulesQuery($id, $validated['date'] ?? null));

        return response()->json($data);
    }

    /** GET /api/v1/routes/{id} — mismo cuerpo que GET .../schedules (detalle + paradas + geometrías). */
    public function show(Request $request, string $id, GetRouteSchedulesHandler $handler): JsonResponse
    {
        return $this->schedules($request, $id, $handler);
    }

    public function showSnapshot(string $id, GetRouteSnapshotHandler $handler): JsonResponse
    {
        $data = $handler->handle(new GetRouteSnapshotQuery($id));

        if (!$data) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($data);
    }
}
