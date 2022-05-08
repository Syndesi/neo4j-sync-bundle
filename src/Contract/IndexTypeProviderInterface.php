<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\Enum\IndexType;

interface IndexTypeProviderInterface
{
    public function getIndexType(): IndexType;
}
