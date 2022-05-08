<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\IndexTypeProviderInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;

class StaticIndexTypeProvider implements IndexTypeProviderInterface
{
    public function __construct(
        private readonly IndexType $indexType = IndexType::BTREE
    ) {
    }

    public function getIndexType(): IndexType
    {
        return $this->indexType;
    }
}
