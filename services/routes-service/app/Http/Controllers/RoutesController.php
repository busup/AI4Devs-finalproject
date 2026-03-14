<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoutesController extends Controller
{
    /**
     * Placeholder: list routes (Lines, Stops, Schedules).
     * Align with docs/4_especificacion_api.md when implementing.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [],
            'message' => 'Routes service placeholder',
        ]);
    }
}
