<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

interface RelationNodeLabelProviderInterface
{
    public function getNodeLabel(object $entity, Node $nodeWithoutRelations): NodeLabel;
}
