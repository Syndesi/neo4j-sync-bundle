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
     * @throws InvalidArgumentException
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
                throw new InvalidArgumentException('All relations need to be for the same relation label.');
            }
        }
        $batch = [];
        foreach ($relations as $relation) {
            $identifier = $relation->getIdentifier();
            if (!$identifier) {
                throw new InvalidArgumentException("All relations need to contain an identifier.");
            }
            $properties = [];
            foreach ($relation->getProperties() as $property) {
                if ($property->getName() === $identifier->getName()) {
                    // id is not a property, it cannot be changed once set
                    continue;
                }
                $properties[$property->getName()] = $property->getValue();
            }
            $batch[] = [
                'id' => $identifier->getValue(),
                'childId' => $relation->getRelatesFromIdentifier()->getValue(),
                'parentId' => $relation->getRelatesToIdentifier()->getValue(),
                'properties' => $properties,
            ];
        }

        $identifier = $relations[0]->getIdentifier();
        if (!$identifier) {
            throw new InvalidArgumentException("All relations need to contain an identifier.");
        }

        return [new Statement(
            sprintf(
                "UNWIND \$batch as row\n".
                "MATCH\n".
                "  (child:%s {%s: row.childId}),\n".
                "  (parent:%s {%s: row.parentId})\n".
                "%s (child)-[relation:%s {%s: row.id}]->(parent)\n".
                "SET relation += row.properties",
                (string) $relations[0]->getRelatesFromLabel(),
                $relations[0]->getRelatesFromIdentifier()->getName(),
                (string) $relations[0]->getRelatesToLabel(),
                $relations[0]->getRelatesToIdentifier()->getName(),
                $createType->value,
                (string) $relations[0]->getLabel(),
                $identifier->getName()
            ),
            [
                'batch' => $batch,
            ]
        )];
    }
}
