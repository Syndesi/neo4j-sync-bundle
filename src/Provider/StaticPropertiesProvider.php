<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticPropertiesProvider implements PropertiesProviderInterface
{
    /**
     * @param Property[] $properties
     *
     * @throws DuplicatePropertiesException
     */
    public function __construct(
        private readonly array $properties
    ) {
    }

    public function getProperties(?object $entity = null): array
    {
        return $this->properties;
    }
}
