<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Doctrine\ORM\Mapping\Entity;
use Exception;
use ReflectionClass;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Object\EntityObject;

class EntityReader
{
    private Neo4jNormalizer $normalizer;

    public function __construct(Neo4jNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @throws ReflectionException
     */
    public function isEntitySupported(object $entity): bool
    {
        return $this->isEntityClassSupported(get_class($entity));
    }

    /**
     * @throws ReflectionException
     */
    public function isEntityClassSupported(string $class): bool
    {
        $reflectionClass = new ReflectionClass($class);

        $isDoctrineEntity = false;
        $isNeo4jSyncEntity = false;

        foreach ($reflectionClass->getAttributes() as $attribute) {
            if ($attribute->newInstance() instanceof Entity) {
                $isDoctrineEntity = true;
            }
            if ($attribute->newInstance() instanceof Node) {
                $isNeo4jSyncEntity = true;
            }
        }

        if (!$isDoctrineEntity || !$isNeo4jSyncEntity) {
            return false;
        }

        return true;
    }

    /**
     * @throws
     */
    public function getEntityObject(object $entity): EntityObject
    {
        $class = get_class($entity);
        if (!$this->isEntitySupported($entity)) {
            throw new UnsupportedEntityException(sprintf('Entity of class %s is not supported.', $class));
        }

        $entityObject = new EntityObject();
        $entityObject->setEntityClass($class);
        $nodeAttributeCount = 0;
        foreach ((new ReflectionClass($class))->getAttributes() as $attribute) {
            if ($attribute->newInstance() instanceof Node) {
                if ($nodeAttributeCount >= 1) {
                    throw new Exception('Only one node attribute per class is supported.');
                }
                /**
                 * @var Node $nodeAttribute
                 */
                $nodeAttribute = $attribute->newInstance();
                $entityObject->setNodeAttribute($nodeAttribute);
                ++$nodeAttributeCount;
            }
        }
        $entityObject->setData(
            $this->normalizer->normalize(
                $entity,
                null,
                [
                    'groups' => $entityObject->getNodeAttribute()->getSerializationGroup(),
                ]
            )
        );
        $properties = $entityObject->getData();
        foreach ($entityObject->getNodeAttribute()->getRelations() as $relation) {
            unset($properties[$relation->getTargetValue()]);
        }
        $entityObject->setProperties($properties);

        return $entityObject;
    }
}
