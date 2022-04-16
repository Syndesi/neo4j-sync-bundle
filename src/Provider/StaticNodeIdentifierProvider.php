<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticNodeIdentifierProvider implements NodeIdentifierProviderInterface
{
    public function __construct(
        private readonly Property $nodeIdentifier
    ) {
    }

    public function getNodeIdentifier(object $entity): Property
    {
        return $this->nodeIdentifier;
    }
}
