<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class DeleteRelationsFromNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * Returns a statement for deleting all relationships where the child node is the given node.
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public static function build(Node $node): array
    {
        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (child:%s {%s: $%s})\n".
                "  -[relation {_managedBy: \$_managedBy}]->\n".
                "  (parent)\n".
                "DELETE relation",
                (string) $node->getLabel(),
                $node->getIdentifier()->getName(),
                $node->getIdentifier()->getName()
            ),
            [
                $node->getIdentifier()->getName() => $node->getIdentifier()->getValue(),
                '_managedBy' => (string) $node->getLabel(),
            ]
        )];
    }
}
