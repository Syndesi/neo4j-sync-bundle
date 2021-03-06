<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class StaticRelationLabelProvider implements RelationLabelProviderInterface
{
    public function __construct(
        private readonly RelationLabel $label
    ) {
    }

    public function getLabel(?object $entity = null): RelationLabel
    {
        return $this->label;
    }
}
