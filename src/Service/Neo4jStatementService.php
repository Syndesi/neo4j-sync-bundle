<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Object\EntityDataObject;

class Neo4jStatementService
{
    private EntityDataObjectService $entityDataObjectService;

    public function __construct(EntityDataObjectService $entityDataObjectService)
    {
        $this->entityDataObjectService = $entityDataObjectService;
    }

    /**
     * @return Statement[]
     *
     * @throws
     */
    public function getCreateStatement(object $entity): array
    {
        $entityDataObject = $this->entityDataObjectService->getEntityDataObject($entity);

        // create entity node itself
        $propertyString = [];
        foreach ($entityDataObject->getProperties() as $key => $value) {
            $propertyString[] = sprintf('%s: $%s', $key, $key);
        }
        $propertyString = implode(', ', $propertyString);
        $statements = [];
        $statements[] = new Statement(
            sprintf(
                'CREATE (n:%s {%s})',
                $entityDataObject->getNodeAttribute()->getLabel(),
                $propertyString
            ),
            $entityDataObject->getData()
        );

        // create relations
        foreach ($entityDataObject->getNodeAttribute()->getRelations() as $relation) {
            $statements[] = new Statement(
                sprintf(
                    "MATCH\n".
                    "  (child:%s),\n".
                    "  (parent:%s)\n".
                    "WHERE child.%s = \$childId AND parent.%s = \$parentId\n".
                    "CREATE (child)-[r:%s]->(parent)\n".
                    'RETURN type(r)',
                    $entityDataObject->getNodeAttribute()->getLabel(),
                    $relation->getTargetLabel(),
                    $entityDataObject->getNodeAttribute()->getId(),
                    $relation->getTargetProperty(),
                    $relation->getLabel()
                ),
                [
                    'childId' => $entityDataObject->getData()[$entityDataObject->getNodeAttribute()->getId()],
                    'parentId' => $entityDataObject->getData()[$relation->getTargetValue()],
                ]
            );
        }

        return $statements;
    }

    /**
     * @return Statement[]
     */
    public function getUpdateStatement(object $entity): array
    {
        $entityDataObject = $this->entityDataObjectService->getEntityDataObject($entity);

        $updatePropertyStrings = [];
        foreach ($entityDataObject->getProperties() as $key => $value) {
            $updatePropertyStrings[] = sprintf("SET n.%s = \$%s\n", $key, $key);
        }

        $statements = [];
        $statements[] = new Statement(
            sprintf(
                "MATCH (n:%s {%s: \$%s})\n".
                '%s',
                $entityDataObject->getNodeAttribute()->getLabel(),
                $entityDataObject->getNodeAttribute()->getId(),
                $entityDataObject->getNodeAttribute()->getId(),
                implode($updatePropertyStrings)
            ),
            $entityDataObject->getData()
        );

        // todo update relations
        return $statements;
    }

    /**
     * @throws MissingIdPropertyException
     */
    public function getDeleteStatement(object $entity): Statement
    {
        $entityDataObject = $this->entityDataObjectService->getEntityDataObject($entity);
        $idPropertyName = $this->getIdPropertyName($entityDataObject);
        $idPropertyValue = $this->getIdPropertyValue($entityDataObject);

        return new Statement(
            sprintf(
                'MATCH (n:%s {%s: $%s}) DETACH DELETE n',
                $entityDataObject->getNodeAttribute()->getLabel(),
                $idPropertyName,
                $idPropertyName
            ),
            [
                $idPropertyName => $idPropertyValue,
            ]
        );
    }

    private function getIdPropertyName(EntityDataObject $object): string
    {
        return $object->getNodeAttribute()->getId();
    }

    /**
     * @throws MissingIdPropertyException
     */
    private function getIdPropertyValue(EntityDataObject $object)
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
