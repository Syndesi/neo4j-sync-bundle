<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;

interface IndexStatementBuilderInterface
{
    /**
     * @return Statement[]
     */
    public static function build(Index $index): array;
}
