<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\CreateType;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Object\EntityObject;

class Neo4jStatementHelper
{
    private EntityReader $entityReader;
    private int $pageSize;
    private CreateType $defaultCreateType;

    public function __construct(EntityReader $entityReader, int $pageSize, CreateType $defaultCreateType)
    {
        $this->entityReader = $entityReader;
        $this->pageSize = $pageSize;
        $this->defaultCreateType = $defaultCreateType;
    }

    /**
     * Notice: All passed entities must be of the same type.
     *
     * @param object[] $entities
     *
     * @return Statement[]
     *
     * @throws UnsupportedEntityException
     */
    public function getNodeStatementsForEntityList(array $entities, ?CreateType $createType = null): array
    {
        if (0 === count($entities)) {
            return [];
        }
        $createType ??= $this->defaultCreateType;
        $firstEntityObject = $this->entityReader->getEntityObject(array_values($entities)[0]);
        $statements = [];
        for ($i = 0; $i < count($entities) / $this->pageSize; ++$i) {
            $batchData = [];
            for ($j = $i * $this->pageSize; $j < min(($i + 1) * $this->pageSize, count($entities)); ++$j) {
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
                    "%s (n:%s {%s: row.%s})\n",
                    $createType->value,
                    $firstEntityObject->getNodeAttribute()->getLabel(),
                    $this->getIdPropertyName($firstEntityObject),
                    $this->getIdPropertyName($firstEntityObject)
                ).
                'SET n += row.properties',
                [
                    'batch' => $batchData,
                ]
            );
        }

        return $statements;
    }

    /**
     * Notice: All passed entities must be of the same type.
     *
     * @param object[] $entities
     *
     * @return Statement[]
     *
     * @throws UnsupportedEntityException|MissingIdPropertyException
     */
    public function getRelationStatementsForEntityList(array $entities, ?CreateType $createType = null): array
    {
        if (0 === count($entities)) {
            return [];
        }
        $createType ??= $this->defaultCreateType;
        $firstEntityObject = $this->entityReader->getEntityObject(array_values($entities)[0]);
        $statements = [];
        for ($i = 0; $i < count($entities) / $this->pageSize; ++$i) {
            $batchData = [];
            for ($j = $i * $this->pageSize; $j < min(($i + 1) * $this->pageSize, count($entities)); ++$j) {
                $entityObject = $this->entityReader->getEntityObject($entities[$j]);
                if ($firstEntityObject->getNodeAttribute()->getLabel() !== $entityObject->getNodeAttribute()->getLabel()) {
                    throw new UnsupportedEntityException('todo');
                }
                foreach ($entityObject->getNodeAttribute()->getRelations() as $relation) {
                    if (!array_key_exists($relation->getLabel(), $batchData)) {
                        $batchData[$relation->getLabel()] = [];
                    }
                    $batchData[$relation->getLabel()][] = [
                        'childId' => $this->getIdPropertyValue($entityObject),
                        'parentId' => $entityObject->getData()[$relation->getTargetValue()],
                    ];
                }
            }

            foreach ($firstEntityObject->getNodeAttribute()->getRelations() as $relation) {
                $statements[] = new Statement(
                    sprintf(
                        "UNWIND \$batch as row\n".
                    "MATCH (child:%s {%s: row.childId})\n".
                    "MATCH (parent:%s {%s: row.parentId})\n".
                    '%s (child)-[relation:%s]->(parent)',
                        $firstEntityObject->getNodeAttribute()->getLabel(),
                        $this->getIdPropertyName($entityObject),
                        $relation->getTargetLabel(),
                        $relation->getTargetProperty(),
                        $createType->value,
                        $relation->getLabel()
                    ),
                    [
                    'batch' => $batchData[$relation->getLabel()],
                ]
                );
            }
        }

        return $statements;
    }

    /**
     * @return Statement[]
     *
     * @throws
     */
    public function getNodeStatements(object $entity, ?CreateType $createType = null): array
    {
        $createType ??= $this->defaultCreateType;
        $entityObject = $this->entityReader->getEntityObject($entity);
        $propertyString = [];
        foreach ($entityObject->getProperties() as $key => $value) {
            $propertyString[] = sprintf('%s: $%s', $key, $key);
        }
        $propertyString = implode(', ', $propertyString);

        return [
            new Statement(
                sprintf(
                    '%s (n:%s {%s})',
                    $createType->value,
                    $entityObject->getNodeAttribute()->getLabel(),
                    $propertyString
                ),
                $entityObject->getData()
            ),
        ];
    }

    /**
     * @return Statement[]
     *
     * @throws
     */
    public function getRelationStatements(object $entity, ?CreateType $createType = null): array
    {
        $createType ??= $this->defaultCreateType;
        $entityObject = $this->entityReader->getEntityObject($entity);
        $statements = [];
        foreach ($entityObject->getNodeAttribute()->getRelations() as $relation) {
            if (CreateType::CREATE != $createType->value) {
                // identify and remove existing relation statements
                $statements[] = new Statement(
                    sprintf(
                        "MATCH\n".
                        "  (child:%s {%s: \$childId})\n".
                        "  -[relation:%s]->\n".
                        "  (parent:%s)\n".
                        "WHERE parent.%s <> \$parentId\n".
                        'DELETE relation',
                        $entityObject->getNodeAttribute()->getLabel(),
                        $this->getIdPropertyName($entityObject),
                        $relation->getLabel(),
                        $relation->getTargetLabel(),
                        $relation->getTargetProperty()
                    ),
                    [
                        'childId' => $this->getIdPropertyValue($entityObject),
                        'parentId' => $entityObject->getData()[$relation->getTargetValue()],
                    ]
                );
            }

            // create/merge relation statement
            $statements[] = new Statement(
                sprintf(
                    "MATCH\n".
                    "  (child:%s {%s: \$childId}),\n".
                    "  (parent:%s {%s: \$parentId})\n".
                    '%s (child)-[r:%s]->(parent)',
                    $entityObject->getNodeAttribute()->getLabel(),
                    $this->getIdPropertyName($entityObject),
                    $relation->getTargetLabel(),
                    $relation->getTargetProperty(),
                    $createType->value,
                    $relation->getLabel()
                ),
                [
                    'childId' => $this->getIdPropertyValue($entityObject),
                    'parentId' => $entityObject->getData()[$relation->getTargetValue()],
                ]
            );
        }

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
                "MATCH\n".
                "  (n:%s {%s: $%s})\n".
                'DETACH DELETE n',
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
