<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Iterator;
use Laudis\Neo4j\Databags\Statement;

interface PaginatedStatementProviderInterface extends Iterator
{
    public const PAGE_SIZE = 500;

    /**
     * @return Statement[]
     */
    public function current(): array;
}
