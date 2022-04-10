<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Object\EntityObject;

class Neo4jStatementHelper
{
    private EntityReader $entityReader;
    public const PAGE_SIZE = 100;

    public function __construct(EntityReader $entityReader)
    {
        $this->entityReader = $entityReader;
    }

    /**
     * Important: All passed entities must be of the same type!
     *
     * @param object[] $entities
     *
     * @return Statement[]
     *
     * @throws UnsupportedEntityException
     */
    public function getNodeCreateStatementsForEntityList(array $entities): array
    {
        if (0 === count($entities)) {
            return [];
        }
        $firstEntityObject = $this->entityReader->getEntityObject(array_values($entities)[0]);
        $statements = [];
        for ($i = 0; $i < count($entities) / self::PAGE_SIZE; ++$i) {
            $batchData = [];
            for ($j = $i * self::PAGE_SIZE; $j < min(($i + 1) * self::PAGE_SIZE, count($entities)); ++$j) {
                $entityObject = $this->entityReader->getEntityObject($entities[$j]);
                if ($firstEntityObject->getNodeAttribute()->getLabel() !== $entityObject->getNodeAttribute()->getLabel()) {
                    throw new UnsupportedEntityException('todo');
                }
                $batchData[] = [
                    $this->getIdPropertyName($entityObject) => $this->getIdPropertyValue($entityObject),
                    'properties' => $entityObject->getProperties(),
                ];
            }
            // see https://medium.com/neo4j/5-tips-tricks-for-fast-batched-updates-of-graph-structures-with-neo4j-and-cypher-73c7f693c8cc#9be3
            $statements[] = new Statement(
                'UNWIND $batch as row'."\n".
                sprintf(
//                    "MERGE (n:%s {%s: row.%s})\n",
                    "CREATE (n:%s {%s: row.%s})\n",
                    $firstEntityObject->getNodeAttribute()->getLabel(),
                    $this->getIdPropertyName($firstEntityObject),
                    $this->getIdPropertyName($firstEntityObject)
                ).
//                "ON CREATE SET n += row.properties",
                'SET n += row.properties',
                [
                    'batch' => $batchData,
                ]
            );
        }

        return $statements;
    }

    /**
     * Important: All passed entities must be of the same type!
     *
     * @param object[] $entities
     *
     * @return Statement[]
     *
     * @throws UnsupportedEntityException
     */
    public function getRelationCreateStatementsForEntityList(array $entities): array
    {
        if (0 === count($entities)) {
            return [];
        }
        $firstEntityObject = $this->entityReader->getEntityObject(array_values($entities)[0]);
        $statements = [];
        for ($i = 0; $i < count($entities) / self::PAGE_SIZE; ++$i) {
            $batchData = [];
            for ($j = $i * self::PAGE_SIZE; $j < min(($i + 1) * self::PAGE_SIZE, count($entities)); ++$j) {
                $entityObject = $this->entityReader->getEntityObject($entities[$j]);
                if ($firstEntityObject->getNodeAttribute()->getLabel() !== $entityObject->getNodeAttribute()->getLabel()) {
                    throw new UnsupportedEntityException('todo');
                }
                $batchData[] = [
                    $this->getIdPropertyName($entityObject) => $this->getIdPropertyValue($entityObject),
                    'properties' => $entityObject->getProperties(),
                ];
            }
            $statements[] = new Statement(
                'UNWIND $batch as row'."\n".
                sprintf(
//                    "MERGE (n:%s {%s: row.%s})\n",
                    "CREATE (n:%s {%s: row.%s})\n",
                    $firstEntityObject->getNodeAttribute()->getLabel(),
                    $this->getIdPropertyName($firstEntityObject),
                    $this->getIdPropertyName($firstEntityObject)
                ).
//                "ON CREATE SET n += row.properties",
                'SET n += row.properties',
                [
                    'batch' => $batchData,
                ]
            );
        }

        return $statements;
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
