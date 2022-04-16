<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Property;

interface NodePropertiesProviderInterface
{
    /**
     * @return Property[]
     */
    public function getNodeProperties(object $entity): array;
}
