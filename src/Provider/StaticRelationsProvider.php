<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class StaticRelationsProvider implements RelationsProviderInterface
{
    public function __construct(
        /**
         * @var Relation[] $nodeRelations
         */
        private readonly array $nodeRelations
    ) {
    }

    public function getRelations(object $entity): array
    {
        return $this->nodeRelations;
    }
}
