<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationNodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class StaticRelationNodeLabelProvider implements RelationNodeLabelProviderInterface
{
    public function __construct(
        private readonly NodeLabel $nodeLabel
    ) {
    }

    public function getNodeLabel(object $entity, Node $nodeWithoutRelations): NodeLabel
    {
        return $this->nodeLabel;
    }
}
