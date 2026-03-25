<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Persistence\Eloquent;

use PlanificationManagement\Domain\Model\{Service, ServiceAssignment, ServiceStop};
use PlanificationManagement\Domain\Model\ValueObject\{
    AssignmentStatus, DriverLogicalId, PlanificationId, RouteSnapshotRefId, SequenceOrder,
    ServiceAssignmentId, ServiceId, ServiceStatus, ServiceStopId, StopLogicalId, TimeOfDay, VehicleLogicalId
};
use PlanificationManagement\Domain\Repository\ServiceRepository;

final class EloquentServiceRepository implements ServiceRepository
{
    public function findById(ServiceId $id): ?Service
    {
        $model = EloquentService::with(['stops', 'assignments'])->find($id->value);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByPlanification(PlanificationId $planificationId): array
    {
        return EloquentService::with(['stops', 'assignments'])
            ->where('planification_id', $planificationId->value)
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function findByDate(\DateTimeImmutable $date): array
    {
        return EloquentService::with(['stops', 'assignments'])
            ->whereDate('service_date', $date->format('Y-m-d'))
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->toArray();
    }

    public function save(Service $service): void
    {
        $model = EloquentService::updateOrCreate(
            ['id' => $service->id()->value],
            [
                'planification_id'      => $service->planificationId()->value,
                'route_snapshot_ref_id' => $service->routeSnapshotRefId()->value,
                'service_date'          => $service->serviceDate(),
                'departure_time'        => $service->departureTime()->value,
                'capacity'              => $service->capacity(),
                'status'                => $service->status()->value,
                'cancellation_reason'   => $service->cancellationReason(),
                'created_at'            => $service->createdAt(),
                'updated_at'            => $service->updatedAt(),
            ]
        );

        $model->stops()->delete();
        foreach ($service->stops() as $stop) {
            $model->stops()->create([
                'id'              => $stop->id()->value,
                'stop_logical_id' => $stop->stopLogicalId()->value,
                'sequence_order'  => $stop->sequenceOrder()->value,
                'scheduled_time'  => $stop->scheduledTime()->value,
                'active'          => $stop->active(),
                'pickup_allowed'  => $stop->pickupAllowed(),
                'dropoff_allowed' => $stop->dropoffAllowed(),
            ]);
        }

        $model->assignments()->delete();
        foreach ($service->assignments() as $asg) {
            $model->assignments()->create([
                'id'                 => $asg->id()->value,
                'vehicle_logical_id' => $asg->vehicleLogicalId()?->value,
                'driver_logical_id'  => $asg->driverLogicalId()?->value,
                'assignment_status'  => $asg->assignmentStatus()->value,
                'provider'           => $asg->provider(),
                'decision_reason'    => $asg->decisionReason(),
                'assigned_at'        => $asg->assignedAt(),
                'created_at'         => $asg->createdAt(),
            ]);
        }
    }

    public function saveMany(array $services): void
    {
        // Simple iteration. Optimization with insert() can be done if required
        foreach ($services as $service) {
            $this->save($service);
        }
    }

    private function toDomain(EloquentService $m): Service
    {
        $stops = $m->stops->map(fn($s) => new ServiceStop(
            ServiceStopId::fromString($s->id),
            ServiceId::fromString($m->id),
            StopLogicalId::fromString($s->stop_logical_id),
            new SequenceOrder((int)$s->sequence_order),
            new TimeOfDay($s->scheduled_time),
            $s->active,
            $s->pickup_allowed,
            $s->dropoff_allowed,
        ))->toArray();

        $assignments = $m->assignments->map(fn($a) => new ServiceAssignment(
            ServiceAssignmentId::fromString($a->id),
            ServiceId::fromString($m->id),
            $a->vehicle_logical_id ? VehicleLogicalId::fromString($a->vehicle_logical_id) : null,
            $a->driver_logical_id ? DriverLogicalId::fromString($a->driver_logical_id) : null,
            AssignmentStatus::from($a->assignment_status),
            $a->provider,
            $a->decision_reason,
            $a->assigned_at?->toImmutable(),
            $a->created_at->toImmutable(),
        ))->toArray();

        return Service::reconstitute(
            ServiceId::fromString($m->id),
            PlanificationId::fromString($m->planification_id),
            RouteSnapshotRefId::fromString($m->route_snapshot_ref_id),
            $m->service_date->toImmutable(),
            new TimeOfDay($m->departure_time),
            (int)$m->capacity,
            ServiceStatus::from($m->status),
            $m->cancellation_reason,
            $m->created_at->toImmutable(),
            $m->updated_at->toImmutable(),
            $stops,
            $assignments,
        );
    }
}
