<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

interface BatchNodeStatementBuilderInterface
{
    /**
     * @param Node[] $nodes Important: All nodes need to be of the same type
     *
     * @return Statement[]
     */
    public static function build(array $nodes): array;
}
