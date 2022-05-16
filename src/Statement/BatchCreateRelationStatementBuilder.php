<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\BatchRelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class BatchCreateRelationStatementBuilder implements BatchRelationStatementBuilderInterface
{
    /**
     * @param Relation[] $relations
     *
     * @return Statement[]
     *
     * @throws InvalidArgumentException
     */
    public static function build(array $relations): array
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
            $batch[] = [
                'childId' => $relation->getRelatesFromIdentifier()->getValue(),
                'parentId' => $relation->getRelatesToIdentifier()->getValue(),
                'properties' => [
                    '_' => null,
                    ...$relation->getPropertiesAsAssociativeArray(),
                ],
            ];
        }

        return [new Statement(
            sprintf(
                "UNWIND \$batch as row\n".
                "MATCH\n".
                "  (child:%s {%s: row.childId}),\n".
                "  (parent:%s {%s: row.parentId})\n".
                "CREATE (child)-[relation:%s]->(parent)\n".
                "SET relation += row.properties",
                (string) $relations[0]->getRelatesFromLabel(),
                $relations[0]->getRelatesFromIdentifier()->getName(),
                (string) $relations[0]->getRelatesToLabel(),
                $relations[0]->getRelatesToIdentifier()->getName(),
                (string) $relations[0]->getLabel()
            ),
            [
                'batch' => $batch,
            ]
        )];
    }
}
