<?php
namespace Syndesi\Neo4jSyncBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Entity;
use Exception;
use Laudis\Neo4j\Databags\Statement;
use ReflectionClass;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Attribute\Relation;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Object\EntityDataObject;
use Syndesi\Neo4jSyncBundle\Serializer\Serializer;

class EntityNormalizerService {

    private Serializer $serializer;

    public function __construct(){
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $neo4jObjectNormalizer = new Neo4jObjectNormalizer();
        $this->serializer = new Serializer([$neo4jObjectNormalizer, $normalizer]);
    }


    /**
     * @throws
     */
    public function getEntityDataObject(object $entity): EntityDataObject
    {
        $entityDataObject = new EntityDataObject();

        $class = get_class($entity);
        $reflectionClass = new ReflectionClass($class);

        $isDoctrineEntity = false;
        $isNeo4jSyncEntity = false;

        foreach ($reflectionClass->getAttributes() as $attribute) {
            //echo($attribute->getName()."\n");
            if ($attribute->newInstance() instanceof Entity) {
                $isDoctrineEntity = true;
            }
            if ($attribute->newInstance() instanceof Node) {
                /** @var Node $nodeAttribute */
                $nodeAttribute = $attribute->newInstance();
                $entityDataObject->setNodeAttribute($nodeAttribute);
                $isNeo4jSyncEntity = true;
            }
            if ($attribute->newInstance() instanceof Relation) {
                /** @var Relation $relationAttribute */
                $relationAttribute = $attribute->newInstance();
                $entityDataObject->setRelationAttribute($relationAttribute);
            }
        }

        if (!$isDoctrineEntity) {
            throw new Exception(sprintf("Entity of class %s is not supported because it does not have Attribute %s", get_class($entity), get_class(Entity::class)));
        }

        if (!$isNeo4jSyncEntity) {
            throw new Exception(sprintf("Entity of class %s is not supported because it does not have Attribute %s", get_class($entity), get_class(Node::class)));
        }


        $entityDataObject->setData(
            $this->serializer->normalize(
                $entity,
                null,
                [
                    'groups' => $entityDataObject->getNodeAttribute()->getSerializationGroup()
                ]
            )
        );
        //print_r($entityDataObject->getData());
        //exit;

        return $entityDataObject;
    }

    /**
     * @throws
     */
    public function getCreateStatementForEntity(object $entity){
        $entityDataObject = $this->getEntityDataObject($entity);
        $propertyString = [];
        foreach ($entityDataObject->getData() as $key => $value) {
            $propertyString[] = sprintf('%s: $%s', $key, $key);
        }
        $propertyString = implode(', ', $propertyString);
        return new Statement(
            sprintf(
                'CREATE (n:%s {%s})',
                $entityDataObject->getNodeAttribute()->getLabel(),
                $propertyString
            ),
            $entityDataObject->getData()
        );
    }


}
