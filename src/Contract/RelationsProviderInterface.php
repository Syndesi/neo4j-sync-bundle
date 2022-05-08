<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

interface RelationsProviderInterface
{
    /**
     * @return Relation[]
     */
    public function getRelations(object $entity): array;
}
