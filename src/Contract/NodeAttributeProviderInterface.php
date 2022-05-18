<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface NodeAttributeProviderInterface
{
    /**
     * @param class-string|object $entityOrClassName
     */
    public function getNodeAttribute(string|object $entityOrClassName): ?NodeAttributeInterface;
}
