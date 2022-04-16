<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\Attribute\Node;

interface NodeAttributeProviderInterface
{
    public function getNodeAttribute(object $entity): ?Node;
}
