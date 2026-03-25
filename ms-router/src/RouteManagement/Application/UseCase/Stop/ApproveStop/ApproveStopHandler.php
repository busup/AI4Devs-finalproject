<?php

declare(strict_types=1);

namespace RouteManagement\Application\UseCase\Stop\ApproveStop;

use Illuminate\Support\Facades\DB;
use RouteManagement\Domain\Model\ValueObject\StopId;
use RouteManagement\Domain\Repository\StopRepository;
use RouteManagement\Infrastructure\Outbox\OutboxEventStore;

final readonly class ApproveStopHandler
{
    public function __construct(
        private StopRepository   $stops,
        private OutboxEventStore $outbox,
    ) {}

    public function handle(ApproveStopCommand $command): void
    {
        $id   = StopId::fromString($command->stopId);
        $stop = $this->stops->findById($id)
            ?? throw new \DomainException("Stop {$command->stopId} not found.");

        $stop->approve();

        DB::transaction(function () use ($stop) {
            $this->stops->save($stop);
            $this->outbox->store($stop->pullDomainEvents());
        });
    }
}
