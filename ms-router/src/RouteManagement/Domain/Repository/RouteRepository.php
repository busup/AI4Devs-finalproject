<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Repository;

use RouteManagement\Domain\Model\Route;
use RouteManagement\Domain\Model\ValueObject\RouteId;

interface RouteRepository
{
    public function findById(RouteId $id): ?Route;

    /** @return Route[] */
    public function findAll(): array;

    public function save(Route $route): void;
}
