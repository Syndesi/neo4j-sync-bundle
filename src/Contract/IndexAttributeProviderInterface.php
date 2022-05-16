<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface IndexAttributeProviderInterface
{
    /**
     * @return list<IndexAttributeInterface>
     */
    public function getIndexAttributes(string|object $entityOrClassName): array;
}
