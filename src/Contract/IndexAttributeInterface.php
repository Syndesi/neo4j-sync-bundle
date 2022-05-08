<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Index;

/**
 * Interface for generating node value objects from arbitrary entities.
 */
interface IndexAttributeInterface
{
    public function getIndex(object $entity): Index;
}
