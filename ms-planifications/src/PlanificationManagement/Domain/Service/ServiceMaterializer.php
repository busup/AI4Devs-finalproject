<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Service;

use Illuminate\Support\Str;
use PlanificationManagement\Domain\Model\{Expedition, Planification, Service, ServiceStop};
use PlanificationManagement\Domain\Model\ValueObject\{ServiceId, ServiceStopId};

final readonly class ServiceMaterializer
{
    /**
     * @return Service[]
     */
    public function materialize(Planification $planification, Expedition $expedition): array
    {
        $services = [];
        // Determine practical end date: either Planification logic or 18 months ahead (system safety limit)
        $limitDate = (new \DateTimeImmutable())->add(new \DateInterval('P18M'));
        $endDate   = $planification->dateUntil() < $limitDate ? $planification->dateUntil() : $limitDate;
        
        $currentDate = $planification->dateFrom();

        while ($currentDate <= $endDate) {
            $dayName = $currentDate->format('D'); // Mon, Tue, etc.

            if ($expedition->daysOfWeek()->isActiveOn($dayName) && $planification->shouldRunOn($currentDate)) {
                $service = Service::generate(
                    ServiceId::fromString(Str::uuid()->toString()),
                    $planification->id(),
                    $expedition->routeSnapshotRefId(),
                    $currentDate,
                    $expedition->baseTime(),
                    $expedition->maxSeats(),
                );

                foreach ($expedition->stops() as $expeditionStop) {
                    if (!$expeditionStop->active()) continue;
                    
                    $service->addStop(new ServiceStop(
                        ServiceStopId::fromString(Str::uuid()->toString()),
                        $service->id(),
                        $expeditionStop->stopLogicalId(),
                        $expeditionStop->sequenceOrder(),
                        $expeditionStop->scheduledTime($expedition->baseTime()),
                        true,
                        $expeditionStop->pickupAllowed(),
                        $expeditionStop->dropoffAllowed(),
                    ));
                }
                
                $services[] = $service;
            }
            // Add 1 day
            $currentDate = $currentDate->add(new \DateInterval('P1D'));
        }

        return $services;
    }
}
