<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\BatchRelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class BatchMergeRelationStatementBuilder implements BatchRelationStatementBuilderInterface
{
    /**
     * @param Relation[] $relations
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public static function build(array $relations, CreateType $createType = CreateType::MERGE): array
    {
        if (empty($relations)) {
            return [];
        }
        foreach ($relations as $relation) {
            if (!($relation instanceof Relation)) {
                throw new InvalidArgumentException('All relations need to be of type relation.');
            }
            if (!$relation->getLabel()->isEqualTo($relations[0]->getLabel())) {
                throw new InvalidArgumentException('All relations need to be for the same relation label');
            }
        }
        $batch = [];
        foreach ($relations as $relation) {
            $properties = [];
            foreach ($relation->getProperties() as $property) {
                if ($property->getName() === $relation->getIdentifier()->getName()) {
                    // id is not a property, it cannot be changed once set
                    continue;
                }
                $properties[$property->getName()] = $property->getValue();
            }
            $batch[] = [
                'id' => $relation->getIdentifier()->getValue(),
                'childId' => $relation->getRelatesFromIdentifier()->getValue(),
                'parentId' => $relation->getRelatesToIdentifier()->getValue(),
                'properties' => $properties,
            ];
        }

        return [new Statement(
            sprintf(
                "UNWIND \$batch as row\n".
                "MATCH\n".
                "  (child:%s {%s: row.childId}),\n".
                "  (parent:%s {%s: row.parentId})\n".
                "%s (child)-[relation:%s {%s: row.id}]->(parent)\n".
                "SET relation += row.properties",
                $relation->getRelatesFromLabel(),
                $relation->getRelatesFromIdentifier()->getName(),
                $relation->getRelatesToLabel(),
                $relation->getRelatesToIdentifier()->getName(),
                $createType->value,
                $relation->getLabel(),
                $relation->getIdentifier()->getName()
            ),
            [
                'batch' => $batch,
            ]
        )];
    }
}
