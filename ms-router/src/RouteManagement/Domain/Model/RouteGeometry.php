<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model;

use RouteManagement\Domain\Model\ValueObject\{
    GeometryFormat, GeometryType, RouteGeometryId, RouteSnapshotId
};

final class RouteGeometry
{
    public function __construct(
        private readonly RouteGeometryId  $id,
        private readonly RouteSnapshotId  $routeSnapshotId,
        private readonly GeometryType     $geometryType,
        private readonly GeometryFormat   $format,
        private readonly string           $content,
        private ?string                   $provider     = null,
        private ?int                      $segmentIndex = null,
        private ?float                    $accuracyM    = null,
        private readonly \DateTimeImmutable $createdAt  = new \DateTimeImmutable(),
    ) {}

    public function id(): RouteGeometryId            { return $this->id; }
    public function routeSnapshotId(): RouteSnapshotId { return $this->routeSnapshotId; }
    public function geometryType(): GeometryType     { return $this->geometryType; }
    public function format(): GeometryFormat         { return $this->format; }
    public function content(): string                { return $this->content; }
    public function provider(): ?string              { return $this->provider; }
    public function segmentIndex(): ?int             { return $this->segmentIndex; }
    public function accuracyM(): ?float              { return $this->accuracyM; }
    public function createdAt(): \DateTimeImmutable  { return $this->createdAt; }
}
