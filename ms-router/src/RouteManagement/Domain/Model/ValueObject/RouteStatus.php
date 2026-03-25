<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

enum RouteStatus: string
{
    case Draft    = 'draft';
    case Approved = 'approved';
    case Archived = 'archived';
}
