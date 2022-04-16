<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeRelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class StaticNodeRelationsProvider implements NodeRelationsProviderInterface
{
    public function __construct(
        /**
         * @var Relation[] $nodeRelations
         */
        private readonly array $nodeRelations
    ) {
    }

    public function getNodeRelations(object $entity): array
    {
        return $this->nodeRelations;
    }
}
