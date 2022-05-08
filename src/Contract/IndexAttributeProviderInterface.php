<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface IndexAttributeProviderInterface
{
    public function getIndexAttribute(object $entity): ?IndexAttributeInterface;
}
