<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\RelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class StaticRelationsProvider implements RelationsProviderInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        /**
         * @var Relation[] $nodeRelations
         */
        private readonly array $nodeRelations
    ) {
        foreach ($this->nodeRelations as $nodeRelation) {
            if (!($nodeRelation instanceof Relation)) {
                throw new InvalidArgumentException(sprintf("Element of type %s is not a relation.", get_class($nodeRelation)));
            }
        }
    }

    public function getRelations(object $entity): array
    {
        return $this->nodeRelations;
    }
}
