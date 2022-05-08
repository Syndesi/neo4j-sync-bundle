<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;

interface IndexNameProviderInterface
{
    public function getName(): IndexName;
}
