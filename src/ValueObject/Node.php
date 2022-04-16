<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;

class Node
{
    /**
     * @throws DuplicatePropertiesException
     * @throws MissingIdPropertyException
     */
    public function __construct(
        private readonly NodeLabel $label,
        /**
         * @var Property[] $properties
         */
        private readonly array $properties,
        private readonly Property $identifier,
        /**
         * @var Relation[] $relations
         */
        private readonly array $relations
    ) {
        $propertyNames = array_map(fn ($property) => $property->getName(), $properties);
        if (count(array_unique($propertyNames)) !== count($properties)) {
            throw new DuplicatePropertiesException('Node require each property to have an unique name.');
        }
        if (!in_array($identifier->getName(), $propertyNames)) {
            throw new MissingIdPropertyException(sprintf("Node has identifier with name '%s', but it is not part of its properties.\n".'If it is a Doctrine entity, was it yet persisted and flushed?', $identifier->getName()));
        }
    }

    public function getLabel(): NodeLabel
    {
        return $this->label;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @throws MissingPropertyException
     */
    public function getProperty(string $name): mixed
    {
        foreach ($this->properties as $property) {
            /**
             * @var Property $property
             */
            if ($property->getName() === $name) {
                return $property->getValue();
            }
        }
        throw new MissingPropertyException(sprintf("Unable to find property with name '%s'.", $name));
    }

    public function getIdentifier(): Property
    {
        return $this->identifier;
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
