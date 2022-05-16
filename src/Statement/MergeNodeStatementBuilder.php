<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class MergeNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * Returns a statement for creating/updating a single node. Relationships are not covered.
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public static function build(Node $node): array
    {
        $propertiesString = [];
        foreach ($node->getProperties() as $property) {
            if ($property->getName() === $node->getIdentifier()->getName()) {
                // id is not a property, it cannot be changed once set
                continue;
            }
            $propertiesString[] = sprintf('    node.%s = $%s', $property->getName(), $property->getName());
        }
        $propertiesString = implode(",\n", $propertiesString);

        return [new Statement(
            sprintf(
                "MERGE (node:%s {%s: $%s})\n".
                "ON CREATE\n".
                "  SET\n".
                "%s\n".
                "ON MATCH\n".
                "  SET\n".
                "%s",
                (string) $node->getLabel(),
                $node->getIdentifier()->getName(),
                $node->getIdentifier()->getName(),
                $propertiesString,
                $propertiesString,
            ),
            $node->getPropertiesAsAssociativeArray()
        )];
    }
}
