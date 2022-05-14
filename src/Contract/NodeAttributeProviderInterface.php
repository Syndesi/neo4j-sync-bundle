<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface NodeAttributeProviderInterface
{
    public function getNodeAttribute(string|object $entityOrClassName): ?NodeAttributeInterface;
}
