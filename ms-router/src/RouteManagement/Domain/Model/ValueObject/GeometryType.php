<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

enum GeometryType: string
{
    case Full       = 'full';
    case Simplified = 'simplified';
    case Segment    = 'segment';
    case Provider   = 'provider';
}
