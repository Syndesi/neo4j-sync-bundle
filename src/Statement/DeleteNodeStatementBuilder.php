<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class DeleteNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * Returns a statement for deleting a single node and all relationships it was connected to.
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
                "  (node:%s {%s: $%s})\n".
                'DETACH DELETE node',
                $node->getLabel(),
                $node->getIdentifier()->getName(),
                $node->getIdentifier()->getName()
            ),
            [
                $node->getIdentifier()->getName() => $node->getIdentifier()->getValue(),
            ]
        )];
    }
}
