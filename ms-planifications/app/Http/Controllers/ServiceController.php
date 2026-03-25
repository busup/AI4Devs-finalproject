<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PlanificationManagement\Application\UseCase\Service\UpdateServiceStatus\{UpdateServiceStatusCommand, UpdateServiceStatusHandler};
use PlanificationManagement\Application\UseCase\Service\AssignResources\{AssignResourcesCommand, AssignResourcesHandler};

final class ServiceController extends Controller
{
    public function updateStatus(Request $request, string $id, UpdateServiceStatusHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:confirmed,running,completed,cancelled',
            'reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        $handler->handle(new UpdateServiceStatusCommand(
            $id,
            $validated['status'],
            $validated['reason'] ?? null,
        ));

        return response()->json(['status' => 'updated']);
    }

    public function assignResources(Request $request, string $id, AssignResourcesHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_logical_id' => 'nullable|uuid',
            'driver_logical_id'  => 'nullable|uuid',
            'provider'           => 'nullable|string|max:255',
        ]);

        $assignmentId = $handler->handle(new AssignResourcesCommand(
            $id,
            $validated['vehicle_logical_id'] ?? null,
            $validated['driver_logical_id'] ?? null,
            $validated['provider'] ?? null,
        ));

        return response()->json(['assignment_id' => $assignmentId], 201);
    }
}
