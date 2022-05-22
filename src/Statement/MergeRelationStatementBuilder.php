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
        if (!$relation->getIdentifier()) {
            throw new InvalidArgumentException("Relation must contain identifier.");
        }

        return self::createReturnStatement($relation);
    }

    /**
     * @return Statement[]
     */
    private static function createReturnStatement(Relation $relation): array
    {
        $propertyString = self::createPropertyString($relation);

        /** @psalm-suppress PossiblyNullReference */
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
                /** @phpstan-ignore-next-line */
                $relation->getIdentifier()->getName(),
                /** @phpstan-ignore-next-line */
                $relation->getIdentifier()->getName(),
                $propertyString,
                $propertyString,
            ),
            [
                ...$relation->getPropertiesAsAssociativeArray(),
                '_childId' => $relation->getRelatesFromIdentifier()->getValue(),
                '_parentId' => $relation->getRelatesToIdentifier()->getValue(),
            ]
        )];
    }

    private static function createPropertyString(Relation $relation): string
    {
        $propertiesString = [];
        foreach ($relation->getProperties() as $property) {
            /**
             * @psalm-suppress PossiblyNullReference
             * @phpstan-ignore-next-line
             */
            if ($property->getName() === $relation->getIdentifier()->getName()) {
                // id is not a property, it cannot be changed once set
                continue;
            }
            $propertiesString[] = sprintf('    relation.%s = $%s', $property->getName(), $property->getName());
        }

        return implode(",\n", $propertiesString);
    }
}
