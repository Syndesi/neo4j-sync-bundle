<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface IndexAttributeProviderInterface
{
    /**
     * @param class-string|object $entityOrClassName
     *
     * @return list<IndexAttributeInterface>
     */
    public function getIndexAttributes(string|object $entityOrClassName): array;
}
