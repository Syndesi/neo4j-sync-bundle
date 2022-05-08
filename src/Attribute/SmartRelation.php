<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jSerializerInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationPropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

#[Attribute(Attribute::TARGET_CLASS)]
class SmartRelation implements RelationAttributeInterface
{
    private readonly Neo4jSerializerInterface $serializer;

    public function __construct(
        private readonly RelationLabel $relationLabel,
        private readonly NodeLabel $relatesToNodeLabel,
        private readonly Property $relatesToNodeIdentifier,
        private readonly ?RelationPropertiesProviderInterface $relationPropertiesProvider = null,
        private readonly ?RelationIdentifierProviderInterface $relationIdentifierProvider = null,
        private readonly array $context = []
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws DuplicatePropertiesException
     * @throws MissingIdPropertyException
     * @throws MissingPropertyValueException
     * @throws UnsupportedPropertyNameException
     * @throws ExceptionInterface
     */
    public function getRelation(object $entity, Node $nodeWithoutRelations): \Syndesi\Neo4jSyncBundle\ValueObject\Relation
    {
        $data = $this->serializer->normalize($entity, null, $this->context);

        return new \Syndesi\Neo4jSyncBundle\ValueObject\Relation(
            $this->relationLabel,
            $this->relatesToNodeLabel,
            new Property($this->relatesToNodeIdentifier->getName(), $data[$this->relatesToNodeIdentifier->getName()]),
            $nodeWithoutRelations->getLabel(),
            $nodeWithoutRelations->getIdentifier(),
            $this->relationPropertiesProvider?->getProperties($entity, $nodeWithoutRelations),
            $this->relationIdentifierProvider?->getIdentifier($entity, $nodeWithoutRelations)
        );
    }
}
