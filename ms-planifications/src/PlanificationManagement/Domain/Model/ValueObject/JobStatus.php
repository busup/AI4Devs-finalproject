<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

enum JobStatus: string
{
    case Pending   = 'pending';
    case Running   = 'running';
    case Completed = 'completed';
    case Failed    = 'failed';
}
