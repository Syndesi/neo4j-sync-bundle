<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Entity;
use ReflectionClass;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Normalizer\RamseyUuidNormalizer;
use Syndesi\Neo4jSyncBundle\Object\EntityDataObject;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;

class EntityDataObjectService
{
    private Neo4jSerializer $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $neo4jObjectNormalizer = new Neo4jObjectNormalizer();
        $ramseyUuidNormalizer = new RamseyUuidNormalizer();
        $this->serializer = new Neo4jSerializer([$neo4jObjectNormalizer, $ramseyUuidNormalizer, $normalizer]);
    }

    /**
     * @throws
     */
    public function getEntityDataObject(object $entity): EntityDataObject
    {
        $entityDataObject = new EntityDataObject();
        $entityDataObject->setEntityClass(get_class($entity));

        $class = get_class($entity);
        $reflectionClass = new ReflectionClass($class);

        $isDoctrineEntity = false;
        $isNeo4jSyncEntity = false;

        foreach ($reflectionClass->getAttributes() as $attribute) {
            // echo($attribute->getName()."\n");
            if ($attribute->newInstance() instanceof Entity) {
                $isDoctrineEntity = true;
            }
            if ($attribute->newInstance() instanceof Node) {
                /** @var Node $nodeAttribute */
                $nodeAttribute = $attribute->newInstance();
                $entityDataObject->setNodeAttribute($nodeAttribute);
                $isNeo4jSyncEntity = true;
            }
        }

        if (!$isDoctrineEntity) {
            throw new UnsupportedEntityException(sprintf('Entity of class %s is not supported because it does not have Attribute %s', get_class($entity), Entity::class));
        }

        if (!$isNeo4jSyncEntity) {
            throw new UnsupportedEntityException(sprintf('Entity of class %s is not supported because it does not have Attribute %s', get_class($entity), Node::class));
        }

        $entityDataObject->setData(
            $this->serializer->normalize(
                $entity,
                null,
                [
                    'groups' => $entityDataObject->getNodeAttribute()->getSerializationGroup(),
                ]
            )
        );
        $properties = $entityDataObject->getData();
        foreach ($entityDataObject->getNodeAttribute()->getRelations() as $relation) {
            unset($properties[$relation->getTargetValue()]);
        }
        $entityDataObject->setProperties($properties);

        return $entityDataObject;
    }
}
