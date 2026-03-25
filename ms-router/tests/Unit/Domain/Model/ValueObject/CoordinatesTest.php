<?php

use RouteManagement\Domain\Model\ValueObject\Coordinates;

test('it creates valid coordinates', function () {
    $coords = new Coordinates(40.4168, -3.7038); // Madrid
    
    expect($coords->lat)->toEqual(40.4168)
        ->and($coords->lon)->toEqual(-3.7038);
});

test('it throws exception for invalid latitude', function () {
    expect(fn() => new Coordinates(91.0, 0.0))
        ->toThrow(\InvalidArgumentException::class, "Latitude 91 is out of range [-90, 90].");
});

test('it throws exception for invalid longitude', function () {
    expect(fn() => new Coordinates(0.0, -181.0))
        ->toThrow(\InvalidArgumentException::class, "Longitude -181 is out of range [-180, 180].");
});
