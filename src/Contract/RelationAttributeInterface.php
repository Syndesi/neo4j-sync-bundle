<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

interface RelationAttributeInterface
{

    public function getRelation(object $entity, Node $nodeWithoutRelations): Relation;

}
