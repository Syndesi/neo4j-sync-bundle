<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

interface NodeRelationsProviderInterface
{
    /**
     * @return Relation[]
     */
    public function getNodeRelations(object $entity): array;
}
