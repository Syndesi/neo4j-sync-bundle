<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jSerializerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeRelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class SerializerNodeRelationsProvider implements NodeRelationsProviderInterface
{
    private readonly Neo4jSerializerInterface $serializer;

    public function __construct(
        /**
         * @var $relations Relation[]
         */
        private readonly array $relations,
        private readonly array $context = []
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ]);
    }

    /**
     * @param Property[] $nodeProperties
     *
     * @return Relation[]
     *
     * @throws UnsupportedPropertyNameException
     * @throws DuplicatePropertiesException
     * @throws InvalidArgumentException
     * @throws MissingIdPropertyException
     * @throws MissingPropertyValueException
     * @throws UnsupportedPropertyNameException
     */
    public function getNodeRelations(object $entity, NodeLabel $nodeLabel, array $nodeProperties, Property $nodeIdentifier): array
    {
        $idProperty = null;
        foreach ($nodeProperties as $property) {
            if ($property->getName() == $nodeIdentifier->getName()) {
                $idProperty = $property;
            }
        }
        if (!$idProperty) {
            throw new MissingIdPropertyException('No id property found');
        }

        $data = $this->serializer->normalize($entity, null, $this->context);
        $serializedRelations = [];
        foreach ($this->relations as $relation) {
            /*
             * @var $relation Relation
             */
            $serializedRelations[] = new Relation(
                $relation->getLabel(),
                $relation->getRelatesToLabel(),
                new Property($relation->getRelatesToIdentifier()->getName(), $data[$relation->getRelatesToIdentifier()->getValue()]),
                $nodeLabel,
                $idProperty
            );
        }

        return $serializedRelations;
    }
}
