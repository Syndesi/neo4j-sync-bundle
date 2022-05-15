<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\StatementBuilderInterface;

class DeleteAllNodesAndRelationsLimitedStatementBuilder implements StatementBuilderInterface
{
    public const LIMIT = 10000;

    /**
     * @return Statement[]
     */
    public static function build(): array
    {
        return [new Statement(
            sprintf(
                "MATCH (n)\n".
                "WITH n LIMIT %d\n".
                "DETACH DELETE n",
                self::LIMIT
            ),
            []
        )];
    }
}
