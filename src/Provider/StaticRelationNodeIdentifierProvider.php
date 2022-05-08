<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationNodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticRelationNodeIdentifierProvider implements RelationNodeIdentifierProviderInterface
{
    public function __construct(
        private readonly Property $nodeIdentifier
    ) {
    }

    public function getIdentifier(object $entity, Node $nodeWithoutRelations): Property
    {
        return $this->nodeIdentifier;
    }
}
