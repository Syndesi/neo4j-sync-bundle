<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\IndexNameProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;

class StaticIndexNameProvider implements IndexNameProviderInterface
{
    public function __construct(
        private readonly IndexName $name
    ) {
    }

    public function getName(): IndexName
    {
        return $this->name;
    }
}
