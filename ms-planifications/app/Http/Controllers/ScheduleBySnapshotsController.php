<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PlanificationManagement\Infrastructure\Persistence\Eloquent\EloquentService;

/**
 * Horario operativo del día para varios snapshots (un servicio canónico por snapshot/fecha).
 *
 * @see docs/tareas_tecnicas_horarios_front_rutas_itinerario.md
 */
final class ScheduleBySnapshotsController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'routeSnapshotIds' => 'required|array|max:100',
            'routeSnapshotIds.*' => 'uuid',
        ]);

        $date = $validated['date'];
        /** @var list<string> $snapshotIds */
        $snapshotIds = array_values(array_unique($validated['routeSnapshotIds']));

        $schedules = [];

        foreach ($snapshotIds as $sid) {
            $service = EloquentService::query()
                ->with([
                    'stops' => static fn ($q) => $q->orderBy('sequence_order'),
                ])
                ->where('route_snapshot_ref_id', $sid)
                ->whereDate('service_date', $date)
                ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 ELSE 1 END")
                ->orderBy('departure_time')
                ->orderBy('id')
                ->first();

            if ($service === null) {
                $schedules[$sid] = null;

                continue;
            }

            $stops = $service->stops;
            $first = $stops->first();
            $last = $stops->last();

            $firstTime = $first !== null ? (string) $first->scheduled_time : (string) $service->departure_time;
            $lastTime = $last !== null ? (string) $last->scheduled_time : $firstTime;

            $durationSeconds = max(0, $this->timeToSeconds($lastTime) - $this->timeToSeconds($firstTime));

            $schedules[$sid] = [
                'serviceId' => $service->id,
                'departureTime' => $this->formatShortTime($firstTime),
                'arrivalTime' => $this->formatShortTime($lastTime),
                'durationSeconds' => $durationSeconds,
                'stops' => $stops->map(static function ($s) {
                    return [
                        'stopId' => $s->stop_logical_id,
                        'sequenceOrder' => (int) $s->sequence_order,
                        'scheduledTime' => substr((string) $s->scheduled_time, 0, 5),
                    ];
                })->values()->all(),
            ];
        }

        return response()->json(['schedules' => $schedules]);
    }

    private function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        $h = (int) ($parts[0] ?? 0);
        $m = (int) ($parts[1] ?? 0);
        $s = (int) ($parts[2] ?? 0);

        return $h * 3600 + $m * 60 + $s;
    }

    private function formatShortTime(string $time): string
    {
        return strlen($time) >= 5 ? substr($time, 0, 5) : $time;
    }
}
