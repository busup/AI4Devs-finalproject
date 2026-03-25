<?php

use Illuminate\Support\Str;
use RouteManagement\Domain\Event\{StopApproved, StopCreated};
use RouteManagement\Domain\Model\Stop;
use RouteManagement\Domain\Model\ValueObject\{
    ApprovalStatus, Coordinates, StopId, Timezone
};

test('it creates a stop correctly and emits StopCreated event', function () {
    $uuid = Str::uuid()->toString();
    $stopId = StopId::fromString($uuid);
    $coords = new Coordinates(41.3851, 2.1734); // Barcelona
    $tz = new Timezone('Europe/Madrid');

    $stop = Stop::create(
        $stopId,
        'Plaza Catalunya',
        'Center',
        $coords,
        $tz,
        true,
        ['is_hub' => true]
    );

    expect($stop->id()->value)->toBe($uuid)
        ->and($stop->name())->toBe('Plaza Catalunya')
        ->and($stop->address())->toBe('Center')
        ->and($stop->approvalStatus())->toBe(ApprovalStatus::Pending)
        ->and($stop->location()->lat)->toBe(41.3851);

    $events = $stop->pullDomainEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(StopCreated::class)
        ->and($events[0]->aggregateId())->toBe($uuid);
});

test('it approves a pending stop', function () {
    $stop = Stop::create(
        StopId::fromString(Str::uuid()->toString()),
        'Plaza Catalunya',
        null,
        new Coordinates(41.3851, 2.1734),
        new Timezone('Europe/Madrid'),
        true
    );
    $stop->pullDomainEvents(); // Clear StopCreated

    expect($stop->approvalStatus())->toBe(ApprovalStatus::Pending);

    $stop->approve();

    expect($stop->approvalStatus())->toBe(ApprovalStatus::Approved);
    
    $events = $stop->pullDomainEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(StopApproved::class);
});
