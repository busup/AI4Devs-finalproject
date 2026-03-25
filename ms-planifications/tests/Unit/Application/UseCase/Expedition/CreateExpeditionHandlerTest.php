<?php

use PlanificationManagement\Application\UseCase\Expedition\CreateExpedition\{CreateExpeditionCommand, CreateExpeditionHandler};
use PlanificationManagement\Domain\Repository\ExpeditionRepository;
use PlanificationManagement\Domain\Service\RouteSnapshotValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Mockery as m;

test('it handles expedition creation successfully', function () {
    // Mock DB::transaction
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn($callback) => $callback());

    // Arrange
    $expeditionRepository = m::mock(ExpeditionRepository::class);
    $routeValidator = m::mock(RouteSnapshotValidator::class);

    $routeValidator->shouldReceive('validate')->once();
    $expeditionRepository->shouldReceive('save')->once();

    $handler = new CreateExpeditionHandler($expeditionRepository, $routeValidator);

    $command = new CreateExpeditionCommand(
        'Morning Commute',
        Str::uuid()->toString(),
        31, // Mon-Fri
        '07:30:00',
        [],
        [
            [
                'stopLogicalId'  => Str::uuid()->toString(),
                'sequenceOrder'  => 1,
                'offsetSeconds'  => 0,
                'active'         => true,
                'pickupAllowed'  => true,
                'dropoffAllowed' => false,
            ]
        ],
        [
            [
                'maxSeats' => 50
            ]
        ]
    );

    // Act
    $expeditionId = $handler->handle($command);

    // Assert
    expect($expeditionId)->toBeString()->toHaveLength(36);
    
    // Cleanup
    m::close();
});

test('it fails creation if route snapshot validation fails', function () {
    // Arrange
    $expeditionRepository = m::mock(ExpeditionRepository::class);
    $routeValidator = m::mock(RouteSnapshotValidator::class);

    $routeValidator->shouldReceive('validate')
        ->once()
        ->andThrow(new \DomainException("Route Snapshot does not exist"));

    // Expected not to be called
    $expeditionRepository->shouldReceive('save')->never();

    $handler = new CreateExpeditionHandler($expeditionRepository, $routeValidator);

    $command = new CreateExpeditionCommand(
        'Invalid Commute',
        Str::uuid()->toString(), // fake ID that will "fail" validation
        31,
        '07:30:00',
        [],
        [
            [
                'stopLogicalId'  => Str::uuid()->toString(),
                'sequenceOrder'  => 1,
                'offsetSeconds'  => 0,
            ]
        ],
        [
            ['maxSeats' => 50]
        ]
    );

    // Act & Assert
    expect(fn() => $handler->handle($command))
        ->toThrow(\DomainException::class, "Route Snapshot does not exist");
        
    m::close();
});
