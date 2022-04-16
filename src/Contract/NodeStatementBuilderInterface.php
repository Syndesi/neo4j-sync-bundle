<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

interface NodeStatementBuilderInterface
{
    /**
     * @return Statement[]
     */
    public function getStatements(Node $node): array;
}
