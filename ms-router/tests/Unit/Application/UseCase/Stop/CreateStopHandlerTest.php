<?php

use RouteManagement\Application\UseCase\Stop\CreateStop\{CreateStopCommand, CreateStopHandler};
use RouteManagement\Domain\Repository\StopRepository;
use RouteManagement\Infrastructure\Outbox\OutboxEventStore;
use Illuminate\Support\Facades\DB;
use Mockery as m;

test('it handles stop creation successfully', function () {
    // Mock DB::transaction
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(fn($callback) => $callback());

    // Arrange
    $stopRepository = m::mock(StopRepository::class);
    $outbox = m::mock(OutboxEventStore::class);

    $stopRepository->shouldReceive('save')->once();
    $outbox->shouldReceive('store')->once()->with([]); // No events on creation, only on approval in this implementation

    $handler = new CreateStopHandler($stopRepository, $outbox);

    $command = new CreateStopCommand(
        'Sol',
        40.4168,
        -3.7038,
        'Europe/Madrid',
        'Puerta del Sol',
        true
    );

    // Act
    $stopId = $handler->handle($command);

    // Assert
    expect($stopId)->toBeString()->toHaveLength(36); // Is a valid UUID string
    
    // Cleanup Mockery
    m::close();
});
