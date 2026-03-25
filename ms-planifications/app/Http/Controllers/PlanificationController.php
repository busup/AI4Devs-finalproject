<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PlanificationManagement\Application\UseCase\Planification\CreatePlanification\{CreatePlanificationCommand, CreatePlanificationHandler};
use PlanificationManagement\Application\UseCase\Planification\ActivatePlanification\{ActivatePlanificationCommand, ActivatePlanificationHandler};
use PlanificationManagement\Application\UseCase\Planification\CancelPlanification\{CancelPlanificationCommand, CancelPlanificationHandler};

final class PlanificationController extends Controller
{
    public function store(Request $request, CreatePlanificationHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'expedition_id'    => 'required|uuid',
            'date_from'        => 'required|date_format:Y-m-d',
            'date_until'       => 'required|date_format:Y-m-d|after_or_equal:date_from',
            'exceptions'       => 'nullable|array',
            'exceptions.*'     => 'date_format:Y-m-d',
            'non_working_days' => 'nullable|array',
            'non_working_days.*' => 'date_format:Y-m-d',
        ]);

        $id = $handler->handle(new CreatePlanificationCommand(
            $validated['expedition_id'],
            $validated['date_from'],
            $validated['date_until'],
            $validated['exceptions'] ?? [],
            $validated['non_working_days'] ?? [],
        ));

        return response()->json(['id' => $id], 201);
    }

    public function activate(string $id, ActivatePlanificationHandler $handler): JsonResponse
    {
        $handler->handle(new ActivatePlanificationCommand($id));

        return response()->json(['status' => 'activated']);
    }

    public function cancel(string $id, CancelPlanificationHandler $handler): JsonResponse
    {
        $handler->handle(new CancelPlanificationCommand($id));

        return response()->json(['status' => 'cancelled']);
    }
}
