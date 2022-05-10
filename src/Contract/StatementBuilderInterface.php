<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Laudis\Neo4j\Databags\Statement;

interface StatementBuilderInterface
{
    /**
     * @return Statement[]
     */
    public static function build(): array;
}
