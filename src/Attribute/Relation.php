<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationNodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationNodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationPropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

#[Attribute(Attribute::TARGET_CLASS)]
class Relation implements RelationAttributeInterface
{
    /**
     * This attribute configures how to generate Neo4j relationships from the target class.
     * Usually applied to Doctrine entities, but not dependent on them. Can be used manually.
     *
     * @param RelationLabelProviderInterface           $relationLabelProvider                 provider which returns the relationship's label
     * @param RelationNodeLabelProviderInterface       $relationRelatesToNodeLabelProvider    provider which returns the parent nodes' label
     * @param RelationNodeIdentifierProviderInterface  $relationRelatesToIdentifierProvider   provider which returns the parent nodes' identifier
     * @param RelationNodeLabelProviderInterface       $relationRelatesFromNodeLabelProvider  provider which returns the child nodes' label
     * @param RelationNodeIdentifierProviderInterface  $relationRelatesFromIdentifierProvider provider which returns the child nodes' identifier
     * @param RelationPropertiesProviderInterface|null $relationPropertiesProvider            provider which returns the relationships' properties, optional
     * @param RelationIdentifierProviderInterface|null $relationIdentifierProvider            provider which returns the relationships' identifier, optional
     */
    public function __construct(
        private readonly RelationLabelProviderInterface $relationLabelProvider,
        private readonly RelationNodeLabelProviderInterface $relationRelatesToNodeLabelProvider,
        private readonly RelationNodeIdentifierProviderInterface $relationRelatesToIdentifierProvider,
        private readonly RelationNodeLabelProviderInterface $relationRelatesFromNodeLabelProvider,
        private readonly RelationNodeIdentifierProviderInterface $relationRelatesFromIdentifierProvider,
        private readonly ?RelationPropertiesProviderInterface $relationPropertiesProvider = null,
        private readonly ?RelationIdentifierProviderInterface $relationIdentifierProvider = null
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws DuplicatePropertiesException
     * @throws MissingIdPropertyException
     * @throws MissingPropertyValueException
     */
    public function getRelation(object $entity, Node $nodeWithoutRelations): \Syndesi\Neo4jSyncBundle\ValueObject\Relation
    {
        return new \Syndesi\Neo4jSyncBundle\ValueObject\Relation(
            $this->relationLabelProvider->getRelationLabel($entity, $nodeWithoutRelations),
            $this->relationRelatesToNodeLabelProvider->getNodeLabel($entity, $nodeWithoutRelations),
            $this->relationRelatesToIdentifierProvider->getIdentifier($entity, $nodeWithoutRelations),
            $this->relationRelatesFromNodeLabelProvider->getNodeLabel($entity, $nodeWithoutRelations),
            $this->relationRelatesFromIdentifierProvider->getIdentifier($entity, $nodeWithoutRelations),
            $this->relationPropertiesProvider?->getProperties($entity, $nodeWithoutRelations),
            $this->relationIdentifierProvider?->getIdentifier($entity, $nodeWithoutRelations)
        );
    }
}
