<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\StatementBuilderInterface;

class GetIndicesStatementBuilder implements StatementBuilderInterface
{
    /**
     * @return Statement[]
     */
    public static function build(): array
    {
        return [new Statement('SHOW INDEX', [])];
    }
}
