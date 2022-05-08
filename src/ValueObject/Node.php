<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;

class Node implements Stringable
{
    /**
     * @param NodeLabel  $label      Label of the node
     * @param Property[] $properties Array of properties. Must include the identifier.
     * @param Property   $identifier Name of the identifier must be stored in the Properties' name. Value is ignored.
     * @param Relation[] $relations  Array of relations, optional
     *
     * @throws DuplicatePropertiesException
     * @throws InvalidArgumentException
     * @throws MissingIdPropertyException
     */
    public function __construct(
        private readonly NodeLabel $label,
        private readonly array $properties,
        private readonly Property $identifier,
        private readonly array $relations = []
    ) {
        $propertyNames = [];
        foreach ($properties as $property) {
            if (!($property instanceof Property)) {
                throw new InvalidArgumentException(sprintf("Property of type %s should be of type %s", get_class($property), Property::class));
            }
            $propertyNames[] = $property->getName();
        }
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

    public function getPropertiesAsAssociativeArray(): array
    {
        $associativeArray = [];
        foreach ($this->properties as $property) {
            $associativeArray[$property->getName()] = $property->getValue();
        }

        return $associativeArray;
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

    /**
     * Returns a new instance of a property with both the identifiers name and the identifiers value from the properties.
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public function getIdentifier(): Property
    {
        return new Property($this->identifier->getName(), $this->getProperty($this->identifier->getName()));
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * checks if all relations have a non-null identifier.
     */
    public function areAllRelationsIdentifiable(): bool
    {
        if (empty($this->relations)) {
            return false;
        }
        foreach ($this->relations as $relation) {
            if (!$relation->getIdentifier()) {
                return false;
            }
        }

        return true;
    }

    public function __toString()
    {
        $properties = [];
        foreach ($this->properties as $property) {
            /* @var $property Property */
            $properties[] = sprintf("%s: %s", $property->getName(), $property->getValue());
        }
        $properties = implode(', ', $properties);

        return sprintf("%s {%s}", $this->label, $properties);
    }
}
