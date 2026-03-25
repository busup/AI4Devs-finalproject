<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

enum PlanificationStatus: string
{
    case Draft     = 'draft';
    case Active    = 'active';
    case Cancelled = 'cancelled';
}
