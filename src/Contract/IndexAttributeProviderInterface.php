<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\Attribute\Index;

interface IndexAttributeProviderInterface
{
    /**
     * @return Index[]
     */
    public function getIndexAttributes(string $className): array;
}
