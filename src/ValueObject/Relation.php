<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;

class Relation implements Stringable
{
    /**
     * @throws MissingPropertyValueException
     * @throws DuplicatePropertiesException
     * @throws MissingIdPropertyException
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly RelationLabel $label,
        private readonly NodeLabel $relatesToLabel,
        private readonly Property $relatesToIdentifier,
        private readonly ?NodeLabel $relatesFromLabel = null,
        private readonly ?Property $relatesFromIdentifier = null,
        /**
         * @var Property[]
         */
        private readonly array $properties = [],
        private readonly ?Property $identifier = null
    ) {
        if (null === $relatesToIdentifier->getValue()) {
            throw new MissingPropertyValueException('The value of the relates to property of a relation can not be null.');
        }
        if ($relatesFromIdentifier) {
            if (null === $relatesFromIdentifier->getValue()) {
                throw new MissingPropertyValueException('The value of the relates from property of a relation can not be null.');
            }
        }
        $propertyNames = [];
        foreach ($properties as $property) {
            if (!($property instanceof Property)) {
                throw new InvalidArgumentException(sprintf("Property of type %s should be of type %s", get_class($property), Property::class));
            }
            $propertyNames[] = $property->getName();
        }
        if (count(array_unique($propertyNames)) !== count($properties)) {
            throw new DuplicatePropertiesException('Relation require each property to have an unique name.');
        }
        if ($identifier) {
            if (!in_array($identifier->getName(), $propertyNames)) {
                throw new MissingIdPropertyException(sprintf("Relation has identifier with name '%s', but it is not part of its properties.\n".'If it is a Doctrine entity, was it yet persisted and flushed?', $identifier->getName()));
            }
        }
    }

    public function getLabel(): RelationLabel
    {
        return $this->label;
    }

    public function getRelatesToLabel(): NodeLabel
    {
        return $this->relatesToLabel;
    }

    public function getRelatesToIdentifier(): Property
    {
        return $this->relatesToIdentifier;
    }

    public function getRelatesFromLabel(): ?NodeLabel
    {
        return $this->relatesFromLabel;
    }

    public function getRelatesFromIdentifier(): ?Property
    {
        return $this->relatesFromIdentifier;
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

    public function getIdentifier(): ?Property
    {
        return $this->identifier;
    }

    public function __toString()
    {
        $properties = [];
        foreach ($this->properties as $property) {
            /** @var $property Property */
            $properties[] = sprintf("%s: %s", $property->getName(), $property->getValue());
        }
        $properties = implode(', ', $properties);
        return sprintf(
            "(:%s {%s: %s})-[:%s {%s}]->(:%s {%s: %s})",
            $this->relatesFromLabel,
            $this->relatesFromIdentifier->getName(),
            $this->relatesFromIdentifier->getValue(),
            $this->label,
            $properties,
            $this->relatesToLabel,
            $this->relatesToIdentifier->getName(),
            $this->relatesToIdentifier->getValue()
        );
    }

}
