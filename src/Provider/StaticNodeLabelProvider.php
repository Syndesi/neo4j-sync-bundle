<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class StaticNodeLabelProvider implements NodeLabelProviderInterface
{
    public function __construct(
        private readonly NodeLabel $nodeLabel
    ) {
    }

    public function getNodeLabel(object $entity): NodeLabel
    {
        return $this->nodeLabel;
    }
}
