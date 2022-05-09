<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Property;

interface PropertiesProviderInterface
{
    /**
     * @return Property[]
     */
    public function getProperties(?object $entity = null): array;
}
