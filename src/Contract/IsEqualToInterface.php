<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface IsEqualToInterface
{
    public function isEqualTo(object $element): bool;
}
