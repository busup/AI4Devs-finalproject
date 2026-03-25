<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

enum AssignmentStatus: string
{
    case Pending   = 'pending';
    case Assigned  = 'assigned';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';
}
