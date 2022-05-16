<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\IndexStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class CreateNodeIndexStatementBuilder implements IndexStatementBuilderInterface
{
    public static function build(Index $index): array
    {
        if (!($index->getLabel() instanceof NodeLabel)) {
            throw new InvalidArgumentException("Only node labels are supported for node indices");
        }
        $propertiesString = [];
        foreach ($index->getProperties() as $property) {
            $propertiesString[] = sprintf('node.%s', $property->getName());
        }
        $propertiesString = implode(", ", $propertiesString);

        return [new Statement(
            sprintf(
                "CREATE %s INDEX %s IF NOT EXISTS\n".
                "FOR (node:%s)\n".
                "ON (%s)",
                $index->getType()->value,
                (string) $index->getName(),
                (string) $index->getLabel(),
                $propertiesString
            ),
            []
        )];
    }
}
