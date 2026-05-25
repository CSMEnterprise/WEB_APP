<?php

namespace App\Controllers;

use App\Entity\EBaseEntity;

abstract class BaseController
{
    protected function entityToArray(?EBaseEntity $entity): ?array
    {
        return $entity?->toArray();
    }

    protected function entitiesToArrays(array $entities): array
    {
        return array_map(
            static fn($entity) => $entity instanceof EBaseEntity ? $entity->toArray() : (array) $entity,
            $entities
        );
    }
}
