<?php

use Illuminate\Support\Str;
use PlanificationManagement\Domain\Model\Expedition;
use PlanificationManagement\Domain\Model\ValueObject\{
    DaysOfWeek, ExpeditionId, ExpeditionStatus, RouteSnapshotRefId, TimeOfDay
};

test('it creates an expedition correctly', function () {
    $uuid = Str::uuid()->toString();
    $expeditionId = ExpeditionId::fromString($uuid);
    $routeRefId = RouteSnapshotRefId::fromString(Str::uuid()->toString());

    $expedition = Expedition::create(
        $expeditionId,
        'Morning Shift A',
        $routeRefId,
        new DaysOfWeek(DaysOfWeek::MONDAY | DaysOfWeek::WEDNESDAY | DaysOfWeek::FRIDAY), // 21
        new TimeOfDay('08:00:00')
    );

    expect($expedition->id()->value)->toBe($uuid)
        ->and($expedition->name())->toBe('Morning Shift A')
        ->and($expedition->routeSnapshotRefId()->value)->toBe($routeRefId->value)
        ->and($expedition->daysOfWeek()->includes(DaysOfWeek::MONDAY))->toBeTrue()
        ->and($expedition->daysOfWeek()->includes(DaysOfWeek::TUESDAY))->toBeFalse()
        ->and($expedition->baseTime()->value)->toBe('08:00:00')
        ->and($expedition->status())->toBe(ExpeditionStatus::Draft);
});

test('it activates a draft expedition', function () {
    $expedition = Expedition::create(
        ExpeditionId::fromString(Str::uuid()->toString()),
        'Morning Shift A',
        RouteSnapshotRefId::fromString(Str::uuid()->toString()),
        new DaysOfWeek(DaysOfWeek::MONDAY),
        new TimeOfDay('08:00:00')
    );

    expect($expedition->status())->toBe(ExpeditionStatus::Draft);

    $expedition->activate();

    expect($expedition->status())->toBe(ExpeditionStatus::Active);
});

test('it cannot activate an already active expedition without error but keeps it active', function () {
    $expedition = Expedition::create(
        ExpeditionId::fromString(Str::uuid()->toString()),
        'Morning',
        RouteSnapshotRefId::fromString(Str::uuid()->toString()),
        new DaysOfWeek(DaysOfWeek::MONDAY),
        new TimeOfDay('08:00:00')
    );

    $expedition->activate();
    $expedition->activate(); // Should not throw

    expect($expedition->status())->toBe(ExpeditionStatus::Active);
});
