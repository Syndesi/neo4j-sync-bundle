<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\IdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticIdentifierProvider implements IdentifierProviderInterface
{
    public function __construct(
        private readonly Property $identifier
    ) {
    }

    public function getIdentifier(object $entity): Property
    {
        return $this->identifier;
    }
}
