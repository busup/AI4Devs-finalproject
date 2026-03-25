<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

enum ServiceStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case Running   = 'running';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
