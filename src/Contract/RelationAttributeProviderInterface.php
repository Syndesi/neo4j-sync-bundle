<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface RelationAttributeProviderInterface
{
    public function getRelationAttribute(string|object $entityOrClassName): ?RelationAttributeInterface;
}
