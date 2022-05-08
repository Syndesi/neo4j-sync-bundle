<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationPropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticRelationPropertiesProvider implements RelationPropertiesProviderInterface
{
    /**
     * @param Property[] $nodeProperties
     */
    public function __construct(
        private readonly array $nodeProperties
    ) {
    }

    /**
     * @return Property[]
     */
    public function getProperties(object $entity, Node $nodeWithoutRelations): array
    {
        return $this->nodeProperties;
    }
}
