<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\IndexStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class CreateRelationIndexStatementBuilder implements IndexStatementBuilderInterface
{
    public static function build(Index $index): array
    {
        if (!($index->getLabel() instanceof RelationLabel)) {
            throw new InvalidArgumentException("Only relation labels are supported for relation indices");
        }
        $propertiesString = [];
        foreach ($index->getProperties() as $property) {
            $propertiesString[] = sprintf('relation.%s', $property->getName());
        }
        $propertiesString = implode(", ", $propertiesString);

        return [new Statement(
            sprintf(
                "CREATE %s INDEX %s IF NOT EXISTS\n".
                "FOR ()-[relation:%s]-()\n".
                "ON (%s)",
                $index->getType()->value,
                $index->getName(),
                $index->getLabel(),
                $propertiesString
            ),
            []
        )];
    }
}
