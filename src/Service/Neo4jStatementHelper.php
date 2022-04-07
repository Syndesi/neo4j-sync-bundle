<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Object\EntityObject;

class Neo4jStatementHelper
{
    private EntityReader $entityReader;

    public function __construct(EntityReader $entityReader)
    {
        $this->entityReader = $entityReader;
    }

    /**
     * @return Statement[]
     *
     * @throws
     */
    public function getCreateStatements(object $entity): array
    {
        $entityObject = $this->entityReader->getEntityObject($entity);

        // create entity node itself
        $propertyString = [];
        foreach ($entityObject->getProperties() as $key => $value) {
            $propertyString[] = sprintf('%s: $%s', $key, $key);
        }
        $propertyString = implode(', ', $propertyString);
        $statements = [];
        $statements[] = new Statement(
            sprintf(
                'CREATE (n:%s {%s})',
                $entityObject->getNodeAttribute()->getLabel(),
                $propertyString
            ),
            $entityObject->getData()
        );

        // create relations
        foreach ($entityObject->getNodeAttribute()->getRelations() as $relation) {
            $statements[] = new Statement(
                sprintf(
                    "MATCH\n".
                    "  (child:%s),\n".
                    "  (parent:%s)\n".
                    "WHERE child.%s = \$childId AND parent.%s = \$parentId\n".
                    "CREATE (child)-[r:%s]->(parent)\n".
                    'RETURN type(r)',
                    $entityObject->getNodeAttribute()->getLabel(),
                    $relation->getTargetLabel(),
                    $entityObject->getNodeAttribute()->getId(),
                    $relation->getTargetProperty(),
                    $relation->getLabel()
                ),
                [
                    'childId' => $entityObject->getData()[$entityObject->getNodeAttribute()->getId()],
                    'parentId' => $entityObject->getData()[$relation->getTargetValue()],
                ]
            );
        }

        return $statements;
    }

    /**
     * @return Statement[]
     */
    public function getUpdateStatements(object $entity): array
    {
        $entityObject = $this->entityReader->getEntityObject($entity);

        $updatePropertyStrings = [];
        foreach ($entityObject->getProperties() as $key => $value) {
            $updatePropertyStrings[] = sprintf("SET n.%s = \$%s\n", $key, $key);
        }

        $statements = [];
        $statements[] = new Statement(
            sprintf(
                "MATCH (n:%s {%s: \$%s})\n".
                '%s',
                $entityObject->getNodeAttribute()->getLabel(),
                $entityObject->getNodeAttribute()->getId(),
                $entityObject->getNodeAttribute()->getId(),
                implode($updatePropertyStrings)
            ),
            $entityObject->getData()
        );

        // todo update relations
        return $statements;
    }

    /**
     * @return Statement[]
     *
     * @throws MissingIdPropertyException
     */
    public function getDeleteStatements(object $entity): array
    {
        $entityObject = $this->entityReader->getEntityObject($entity);
        $idPropertyName = $this->getIdPropertyName($entityObject);
        $idPropertyValue = $this->getIdPropertyValue($entityObject);

        return [new Statement(
            sprintf(
                'MATCH (n:%s {%s: $%s}) DETACH DELETE n',
                $entityObject->getNodeAttribute()->getLabel(),
                $idPropertyName,
                $idPropertyName
            ),
            [
                $idPropertyName => $idPropertyValue,
            ]
        )];
    }

    private function getIdPropertyName(EntityObject $object): string
    {
        return $object->getNodeAttribute()->getId();
    }

    /**
     * @throws MissingIdPropertyException
     */
    private function getIdPropertyValue(EntityObject $object)
    {
        $idPropertyName = $this->getIdPropertyName($object);
        if (!key_exists($idPropertyName, $object->getData())) {
            throw new MissingIdPropertyException(sprintf("The normalized data of object '%s' does not contain the id attribute with name '%s'", $object->getEntityClass(), $idPropertyName));
        }
        $idPropertyValue = $object->getData()[$idPropertyName];
        if (!$idPropertyValue) {
            throw new MissingIdPropertyException(sprintf("The normalized data of object '%s' must contain a non-null value for the id field with name '%s'", $object->getEntityClass(), $idPropertyName));
        }

        return $idPropertyValue;
    }
}
