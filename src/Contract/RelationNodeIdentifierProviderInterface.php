<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

interface RelationNodeIdentifierProviderInterface
{
    public function getIdentifier(object $entity, Node $nodeWithoutRelations): Property;
}
