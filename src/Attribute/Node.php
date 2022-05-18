<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\IdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeFromNodeInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;

#[Attribute(Attribute::TARGET_CLASS)]
class Node implements NodeAttributeInterface
{
    /**
     * This attribute configures how to generate Neo4j nodes & relationships from the target class.
     * Usually applied to Doctrine entities, but not dependent on them. Can be used manually.
     *
     * @param NodeLabelProviderInterface           $nodeLabelProvider      provider which returns the node's label
     * @param PropertiesProviderInterface          $nodePropertiesProvider provider which returns the node's properties
     * @param IdentifierProviderInterface          $nodeIdentifierProvider provider which returns the node's identifier (name only)
     * @param RelationAttributeFromNodeInterface[] $relations              array of relation attributes
     */
    public function __construct(
        private readonly NodeLabelProviderInterface $nodeLabelProvider,
        private readonly PropertiesProviderInterface $nodePropertiesProvider,
        private readonly IdentifierProviderInterface $nodeIdentifierProvider,
        private readonly array $relations = []
    ) {
    }

    /**
     * @throws DuplicatePropertiesException
     * @throws InvalidArgumentException
     * @throws MissingIdPropertyException
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public function getNode(object $entity): \Syndesi\Neo4jSyncBundle\ValueObject\Node
    {
        $nodeWithoutRelations = new \Syndesi\Neo4jSyncBundle\ValueObject\Node(
            $this->nodeLabelProvider->getLabel($entity),
            $this->nodePropertiesProvider->getProperties($entity),
            $this->nodeIdentifierProvider->getIdentifier($entity)
        );

        $relations = [];
        foreach ($this->relations as $relation) {
            $relationVo = $relation->getRelation($entity, $nodeWithoutRelations);
            try {
                if ($relationVo->getProperty('_managedBy') !== (string) $nodeWithoutRelations->getLabel()) {
                    throw new MissingPropertyException('');
                }
            } catch (MissingPropertyException) {
                throw new MissingPropertyException(sprintf("Relation of type %s does not contain required property with the name '_managedBy' and the node's label as the value.", get_class($relation)));
            }
            $relations[] = $relationVo;
        }

        return new \Syndesi\Neo4jSyncBundle\ValueObject\Node(
            $nodeWithoutRelations->getLabel(),
            $nodeWithoutRelations->getProperties(),
            $nodeWithoutRelations->getIdentifier(),
            $relations
        );
    }

    public function hasRelations(): bool
    {
        return !empty($this->relations);
    }
}
