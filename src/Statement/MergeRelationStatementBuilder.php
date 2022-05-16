<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\RelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class MergeRelationStatementBuilder implements RelationStatementBuilderInterface
{
    /**
     * Returns a statement for creating/updating a single relation.
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     * @throws InvalidArgumentException
     */
    public static function build(Relation $relation): array
    {
        $identifier = $relation->getIdentifier();
        if (!$identifier) {
            throw new InvalidArgumentException("Relation must contain identifier.");
        }
        $propertiesString = [];
        foreach ($relation->getProperties() as $property) {
            if ($property->getName() === $identifier->getName()) {
                // id is not a property, it cannot be changed once set
                continue;
            }
            $propertiesString[] = sprintf('    relation.%s = $%s', $property->getName(), $property->getName());
        }
        $propertiesString = implode(",\n", $propertiesString);

        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (child:%s {%s: \$_childId}),\n".
                "  (parent:%s {%s: \$_parentId})\n".
                "MERGE (child)-[relation:%s {%s: $%s}]->(parent)\n".
                "ON CREATE\n".
                "  SET\n".
                "%s\n".
                "ON MATCH\n".
                "  SET\n".
                "%s",
                (string) $relation->getRelatesFromLabel(),
                $relation->getRelatesFromIdentifier()->getName(),
                (string) $relation->getRelatesToLabel(),
                $relation->getRelatesToIdentifier()->getName(),
                (string) $relation->getLabel(),
                $identifier->getName(),
                $identifier->getName(),
                $propertiesString,
                $propertiesString,
            ),
            [
                ...$relation->getPropertiesAsAssociativeArray(),
                '_childId' => $relation->getRelatesFromIdentifier()->getValue(),
                '_parentId' => $relation->getRelatesToIdentifier()->getValue(),
            ]
        )];
    }
}
