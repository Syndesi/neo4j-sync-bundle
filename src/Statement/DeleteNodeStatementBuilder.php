<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;

class DeleteNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * @throws MissingPropertyException
     */
    public function getStatements(Node $node): array
    {
        return [new Statement(
            sprintf(
                "MATCH\n".
                "  (n:%s {%s: $%s})\n".
                'DETACH DELETE n',
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
