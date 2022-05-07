<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class MergeNodeStatementBuilder implements NodeStatementBuilderInterface
{
    public static function build(Node $node): array
    {
        $propertiesString = [];
        foreach ($node->getProperties() as $property) {
            if ($property->getName() === $node->getIdentifier()->getName()) {
                // id is not a property, it cannot be changed once set
                continue;
            }
            $propertiesString[] = sprintf('    n.%s = $%s', $property->getName(), $property->getName());
        }
        $propertiesString = implode(",\n", $propertiesString);
        return [new Statement(
            sprintf(
                "MERGE (n:%s {%s: $%s})\n".
                "ON CREATE\n".
                "  SET\n".
                "%s\n".
                "ON MATCH\n".
                "  SET\n".
                "%s",
                $node->getLabel(),
                $node->getIdentifier()->getName(),
                $node->getIdentifier()->getName(),
                $propertiesString,
                $propertiesString,
            ),
            $node->getPropertiesAsAssociativeArray()
        )];
    }
}
