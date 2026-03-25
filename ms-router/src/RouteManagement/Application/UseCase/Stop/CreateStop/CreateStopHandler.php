<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\CreateStop;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RouteManagement\Domain\Model\Stop;
use RouteManagement\Domain\Model\ValueObject\{Coordinates, StopId, Timezone};
use RouteManagement\Domain\Repository\StopRepository;
use RouteManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class CreateStopHandler
{
    public function __construct(
        private StopRepository  $stops,
        private OutboxEventStore $outbox,
    ) {}

    public function handle(CreateStopCommand $command): string
    {
        $id   = StopId::fromString(Str::uuid()->toString());
        $stop = Stop::create(
            $id,
            $command->name,
            $command->address,
            new Coordinates($command->lat, $command->lon),
            new Timezone($command->timezone),
            $command->isAccessible,
            $command->metadata,
        );

        DB::transaction(function () use ($stop) {
            $this->stops->save($stop);
            $this->outbox->store($stop->pullDomainEvents());
        });

        return $id->value;
    }
}
