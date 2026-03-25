<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PlanificationManagement\Application\UseCase\Expedition\CreateExpedition\{CreateExpeditionCommand, CreateExpeditionHandler};
use PlanificationManagement\Application\UseCase\Expedition\ActivateExpedition\{ActivateExpeditionCommand, ActivateExpeditionHandler};
use PlanificationManagement\Application\UseCase\Expedition\GetExpedition\{GetExpeditionQuery, GetExpeditionHandler};

final class ExpeditionController
{
    public function store(Request $request, CreateExpeditionHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string',
            'route_snapshot_ref_id' => 'required|uuid',
            'days_of_week_bitmask'  => 'required|integer|min:1|max:127',
            'base_time'             => 'required|date_format:H:i:s',
            'metadata'              => 'nullable|array',
            'stops'                 => 'required|array|min:2',
            'stops.*.stopLogicalId' => 'required|uuid',
            'stops.*.sequenceOrder' => 'required|integer|min:0',
            'stops.*.offsetSeconds' => 'required|integer',
            'stops.*.active'        => 'boolean',
            'stops.*.pickupAllowed' => 'boolean',
            'stops.*.dropoffAllowed'=> 'boolean',
            'capacity_rules'        => 'required|array|min:1',
            'capacity_rules.*.maxSeats' => 'required|integer|min:1',
        ]);

        $command = new CreateExpeditionCommand(
            $validated['name'],
            $validated['route_snapshot_ref_id'],
            (int)$validated['days_of_week_bitmask'],
            $validated['base_time'],
            $validated['metadata'] ?? null,
            $validated['stops'],
            $validated['capacity_rules'],
        );

        $id = $handler->handle($command);

        return response()->json(['id' => $id], 201);
    }

    public function activate(string $id, ActivateExpeditionHandler $handler): JsonResponse
    {
        $handler->handle(new ActivateExpeditionCommand($id));

        return response()->json(['status' => 'activated']);
    }

    public function show(string $id, GetExpeditionHandler $handler): JsonResponse
    {
        $data = $handler->handle(new GetExpeditionQuery($id));

        return response()->json($data);
    }
}
