<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;

#[Attribute(Attribute::TARGET_CLASS)]
class Node implements NodeAttributeInterface
{

    /**
     * @param Relation[] $relations
     */
    public function __construct(
        private readonly NodeLabelProviderInterface $nodeLabelProvider,
        private readonly NodePropertiesProviderInterface $nodePropertiesProvider,
        private readonly NodeIdentifierProviderInterface $nodeIdentifierProvider,
//        private readonly ?NodeRelationsProviderInterface $nodeRelationsProvider = null
        private readonly array $relations = []
    ) {
    }

    /**
     * @throws DuplicatePropertiesException
     * @throws MissingPropertyValueException
     * @throws InvalidArgumentException
     * @throws MissingIdPropertyException
     */
    public function getNode(object $entity): \Syndesi\Neo4jSyncBundle\ValueObject\Node
    {
        $nodeWithoutRelations = new \Syndesi\Neo4jSyncBundle\ValueObject\Node(
            $this->nodeLabelProvider->getNodeLabel($entity),
            $this->nodePropertiesProvider->getNodeProperties($entity),
            $this->nodeIdentifierProvider->getNodeIdentifier($entity)
        );

        $relations = [];
        foreach ($this->relations as $relation) {
            $relations[] = $relation->getNode($entity, $nodeWithoutRelations);
        }

        return new \Syndesi\Neo4jSyncBundle\ValueObject\Node(
            $nodeWithoutRelations->getLabel(),
            $nodeWithoutRelations->getProperties(),
            $nodeWithoutRelations->getIdentifier(),
            $relations
        );
    }
}
