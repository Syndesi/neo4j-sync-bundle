<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticPropertiesProvider implements PropertiesProviderInterface
{
    /**
     * @param Property[] $properties
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly array $properties
    ) {
        foreach ($this->properties as $property) {
            if (!($property instanceof Property)) {
                throw new InvalidArgumentException(sprintf("Element of type %s is not a property.", get_class($property)));
            }
        }
    }

    public function getProperties(?object $entity = null): array
    {
        return $this->properties;
    }
}
