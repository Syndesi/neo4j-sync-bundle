<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class StaticRelationLabelProvider implements RelationLabelProviderInterface
{
    public function __construct(
        private readonly RelationLabel $relationLabel
    ) {
    }

    public function getRelationLabel(object $entity, Node $nodeWithoutRelations): RelationLabel
    {
        return $this->relationLabel;
    }
}
