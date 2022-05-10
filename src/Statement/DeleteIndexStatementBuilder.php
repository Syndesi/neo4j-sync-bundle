<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\IndexStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;

class DeleteIndexStatementBuilder implements IndexStatementBuilderInterface
{
    /**
     * @return Statement[]
     */
    public static function build(Index $index): array
    {
        return [new Statement(sprintf("DROP INDEX %s", $index->getName()), [])];
    }
}
