<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

enum ExpeditionStatus: string
{
    case Draft    = 'draft';
    case Active   = 'active';
    case Archived = 'archived';
}
