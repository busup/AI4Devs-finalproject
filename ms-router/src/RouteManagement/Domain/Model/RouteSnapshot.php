<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model;

use RouteManagement\Domain\Event\RoutePublished;
use RouteManagement\Domain\Model\ValueObject\{RouteId, RouteSnapshotId, VersionNumber};

final class RouteSnapshot
{
    private array $domainEvents = [];
    /** @var RouteStop[] */
    private array $stops = [];
    /** @var RouteGeometry[] */
    private array $geometries = [];

    private function __construct(
        private readonly RouteSnapshotId $id,
        private readonly RouteId         $routeId,
        private readonly VersionNumber   $versionNumber,
        private ?int                     $totalDistanceM,
        private ?int                     $estimatedDurationS,
        private ?\DateTimeImmutable      $validFrom,
        private ?\DateTimeImmutable      $validUntil,
        private ?\DateTimeImmutable      $publishedAt,
        private readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(
        RouteSnapshotId     $id,
        RouteId             $routeId,
        VersionNumber       $versionNumber,
        ?\DateTimeImmutable $validFrom = null,
        ?\DateTimeImmutable $validUntil = null,
    ): self {
        return new self(
            $id, $routeId, $versionNumber,
            null, null, $validFrom, $validUntil, null,
            new \DateTimeImmutable(),
        );
    }

    public static function reconstitute(
        RouteSnapshotId     $id,
        RouteId             $routeId,
        VersionNumber       $versionNumber,
        ?int                $totalDistanceM,
        ?int                $estimatedDurationS,
        ?\DateTimeImmutable $validFrom,
        ?\DateTimeImmutable $validUntil,
        ?\DateTimeImmutable $publishedAt,
        \DateTimeImmutable  $createdAt,
        array               $stops = [],
        array               $geometries = [],
    ): self {
        $self = new self(
            $id, $routeId, $versionNumber,
            $totalDistanceM, $estimatedDurationS,
            $validFrom, $validUntil, $publishedAt, $createdAt,
        );
        $self->stops = $stops;
        $self->geometries = $geometries;
        return $self;
    }

    public function publish(): void
    {
        if ($this->publishedAt !== null) {
            throw new \DomainException('RouteSnapshot is already published.');
        }
        $this->publishedAt = new \DateTimeImmutable();
        $this->domainEvents[] = new RoutePublished(
            $this->routeId,
            $this->id,
            $this->versionNumber,
        );
    }

    public function updateMetrics(int $distanceM, int $durationS): void
    {
        $this->totalDistanceM     = $distanceM;
        $this->estimatedDurationS = $durationS;
    }

    public function addStop(RouteStop $stop): void         { $this->stops[] = $stop; }
    public function addGeometry(RouteGeometry $geo): void  { $this->geometries[] = $geo; }
    public function isPublished(): bool                    { return $this->publishedAt !== null; }

    public function id(): RouteSnapshotId           { return $this->id; }
    public function routeId(): RouteId              { return $this->routeId; }
    public function versionNumber(): VersionNumber  { return $this->versionNumber; }
    public function totalDistanceM(): ?int          { return $this->totalDistanceM; }
    public function estimatedDurationS(): ?int      { return $this->estimatedDurationS; }
    public function validFrom(): ?\DateTimeImmutable { return $this->validFrom; }
    public function validUntil(): ?\DateTimeImmutable { return $this->validUntil; }
    public function publishedAt(): ?\DateTimeImmutable { return $this->publishedAt; }
    public function createdAt(): \DateTimeImmutable  { return $this->createdAt; }
    /** @return RouteStop[] */
    public function stops(): array      { return $this->stops; }
    /** @return RouteGeometry[] */
    public function geometries(): array { return $this->geometries; }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
