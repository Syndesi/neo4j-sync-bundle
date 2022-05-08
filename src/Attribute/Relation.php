<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\IdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;

#[Attribute(Attribute::TARGET_CLASS)]
class Relation implements RelationAttributeInterface
{
    /**
     * This attribute configures how to generate Neo4j relationships from the target class.
     * Usually applied to Doctrine entities, but not dependent on them. Can be used manually.
     *
     * @param RelationLabelProviderInterface   $relationLabelProvider                 provider which returns the relationship's label
     * @param NodeLabelProviderInterface       $relationRelatesToNodeLabelProvider    provider which returns the parent nodes' label
     * @param IdentifierProviderInterface      $relationRelatesToIdentifierProvider   provider which returns the parent nodes' identifier
     * @param NodeLabelProviderInterface       $relationRelatesFromNodeLabelProvider  provider which returns the child nodes' label
     * @param IdentifierProviderInterface      $relationRelatesFromIdentifierProvider provider which returns the child nodes' identifier
     * @param PropertiesProviderInterface|null $relationPropertiesProvider            Provider which returns the relationships' properties. Required if relation is independent.
     * @param IdentifierProviderInterface|null $relationIdentifierProvider            Provider which returns the relationships' identifier. Required if relation is independent.
     */
    public function __construct(
        private readonly RelationLabelProviderInterface $relationLabelProvider,
        private readonly NodeLabelProviderInterface $relationRelatesToNodeLabelProvider,
        private readonly IdentifierProviderInterface $relationRelatesToIdentifierProvider,
        private readonly NodeLabelProviderInterface $relationRelatesFromNodeLabelProvider,
        private readonly IdentifierProviderInterface $relationRelatesFromIdentifierProvider,
        private readonly ?PropertiesProviderInterface $relationPropertiesProvider = null,
        private readonly ?IdentifierProviderInterface $relationIdentifierProvider = null
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws DuplicatePropertiesException
     * @throws MissingIdPropertyException
     * @throws MissingPropertyValueException
     */
    public function getRelation(object $entity): \Syndesi\Neo4jSyncBundle\ValueObject\Relation
    {
        return new \Syndesi\Neo4jSyncBundle\ValueObject\Relation(
            $this->relationLabelProvider->getLabel($entity),
            $this->relationRelatesToNodeLabelProvider->getLabel($entity),
            $this->relationRelatesToIdentifierProvider->getIdentifier($entity),
            $this->relationRelatesFromNodeLabelProvider->getLabel($entity),
            $this->relationRelatesFromIdentifierProvider->getIdentifier($entity),
            $this->relationPropertiesProvider ? $this->relationPropertiesProvider->getProperties($entity) : [],
            $this->relationIdentifierProvider?->getIdentifier($entity)
        );
    }
}
