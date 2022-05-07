<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class DeleteRelationsFromNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * @throws MissingPropertyException
     */
    public static function build(Node $node): array
    {
        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (child:%s {%s: $%s})\n".
                "  -[relation]->\n".
                "  (parent)\n".
                "DELETE relation",
                $node->getLabel(),
                $node->getIdentifier()->getName(),
                $node->getIdentifier()->getName()
            ),
            [
                $node->getIdentifier()->getName() => $node->getProperty($node->getIdentifier()->getName()),
            ]
        )];
    }
}
