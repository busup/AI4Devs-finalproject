<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

enum GeometryFormat: string
{
    case GeoJson  = 'geojson';
    case Polyline = 'polyline';
    case Wkt      = 'wkt';
}
