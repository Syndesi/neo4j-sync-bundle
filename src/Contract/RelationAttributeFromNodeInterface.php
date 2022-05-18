<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

interface RelationAttributeFromNodeInterface
{
    /**
     * @note The returned relation must contain a property with the name '_managedBy' and the node's label as the value.
     */
    public function getRelation(object $entity, Node $nodeWithoutRelations): Relation;
}
