<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;

/**
 * Interface for generating node value objects from arbitrary entities.
 */
interface NodeAttributeInterface
{
    public function getNode(object $entity): Node;

    public function hasRelations(): bool;
}
