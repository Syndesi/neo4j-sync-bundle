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
     * @throws InvalidArgumentException
     */
    public static function build(Relation $relation): array
    {
        if ($relation->getRelatesFromLabel() === null) {
            throw new InvalidArgumentException('relatesFromLabel can not be null.');
        }
        if ($relation->getRelatesFromIdentifier() === null) {
            throw new InvalidArgumentException('relatesFromIdentifier can not be null.');
        }
        $relationPropertyString = [];
        foreach ($relation->getProperties() as $property) {
            // do not filter relation id property
            $relationPropertyString[] = sprintf("%s: $%s", $property->getName(), $property->getName());
        }
        $relationPropertyString = implode(', ', $relationPropertyString);
        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (child:%s {%s: \$_childId}),\n".
                "  (parent:%s {%s: \$_parentId})\n".
                "CREATE (child)-[r:%s {%s}]->(parent)",
                $relation->getRelatesFromLabel(),
                $relation->getRelatesFromIdentifier()->getName(),
                $relation->getRelatesToLabel(),
                $relation->getRelatesToIdentifier()->getName(),
                $relation->getLabel(),
                $relationPropertyString
            ),
            [
                ...$relation->getPropertiesAsAssociativeArray(),
                '_parentId' => $relation->getRelatesToIdentifier()->getValue(),
                '_childId' => $relation->getRelatesFromIdentifier()->getValue()
            ]
        )];
    }
}
