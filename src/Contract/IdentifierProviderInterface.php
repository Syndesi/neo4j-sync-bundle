<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Property;

interface IdentifierProviderInterface
{
    public function getIdentifier(object $entity): Property;
}
