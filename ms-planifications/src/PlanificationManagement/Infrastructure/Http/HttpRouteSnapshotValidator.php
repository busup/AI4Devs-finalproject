<?php

declare(strict_types=1);

namespace PlanificationManagement\Infrastructure\Http;

use Illuminate\Support\Facades\Http;
use PlanificationManagement\Domain\Model\ValueObject\RouteSnapshotRefId;
use PlanificationManagement\Domain\Service\RouteSnapshotValidator;

final class HttpRouteSnapshotValidator implements RouteSnapshotValidator
{
    private string $routerUrl;

    public function __construct()
    {
        // En un entorno real, esto vendría de config('services.router.url')
        $this->routerUrl = rtrim(env('ROUTER_SERVICE_URL', 'http://ms-router:80'), '/');
    }

    public function validate(RouteSnapshotRefId $refId): void
    {
        $response = Http::timeout(3)
            ->withHeaders(['Accept' => 'application/json'])
            ->get("{$this->routerUrl}/api/v1/routes/snapshots/{$refId->value}");

        if ($response->notFound()) {
            throw new \DomainException("RouteSnapshotRefId {$refId->value} no existe en ms-router.");
        }

        if ($response->failed()) {
            throw new \RuntimeException("No se pudo contactar con ms-router o devolvió error.");
        }
    }
}
