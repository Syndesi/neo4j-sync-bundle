<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\RelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class CreateRelationStatementBuilder implements RelationStatementBuilderInterface
{
    /**
     * Returns a statement for creating a relationship between two already existing nodes.
     *
     * @return Statement[]
     *
     * @throws InvalidArgumentException
     */
    public static function build(Relation $relation): array
    {
        $relationPropertyString = [];
        foreach ($relation->getProperties() as $property) {
            // do not filter relation id property
            $relationPropertyString[] = sprintf('%s: $%s', $property->getName(), $property->getName());
        }
        $relationPropertyString = implode(', ', $relationPropertyString);

        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (child:%s {%s: \$_childId}),\n".
                "  (parent:%s {%s: \$_parentId})\n".
                "CREATE (child)-[:%s {%s}]->(parent)",
                (string) $relation->getRelatesFromLabel(),
                $relation->getRelatesFromIdentifier()->getName(),
                (string) $relation->getRelatesToLabel(),
                $relation->getRelatesToIdentifier()->getName(),
                (string) $relation->getLabel(),
                $relationPropertyString
            ),
            [
                ...$relation->getPropertiesAsAssociativeArray(),
                '_parentId' => $relation->getRelatesToIdentifier()->getValue(),
                '_childId' => $relation->getRelatesFromIdentifier()->getValue(),
            ]
        )];
    }
}
