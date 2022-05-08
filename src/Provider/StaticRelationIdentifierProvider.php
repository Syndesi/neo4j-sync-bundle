<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticRelationIdentifierProvider implements RelationIdentifierProviderInterface
{
    public function __construct(
        private readonly Property $relationIdentifier
    ) {
    }

    public function getIdentifier(object $entity, Node $nodeWithoutRelations): Property
    {
        return $this->relationIdentifier;
    }
}
