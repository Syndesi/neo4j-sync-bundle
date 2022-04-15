<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValue;

class Relation
{
    /**
     * @throws MissingPropertyValue
     * @throws DuplicatePropertiesException
     */
    public function __construct(
        private readonly RelationLabel $label,
        /**
         * @var Property[]
         */
        private readonly array $properties,
        private readonly NodeLabel $relatesToLabel,
        private readonly Property $relatesToIdentifier,
        private readonly NodeLabel $relatesFromLabel,
        private readonly Property $relatesFromIdentifier
    ) {
        if (null === $this->relatesToIdentifier->getValue()) {
            throw new MissingPropertyValue('The value of the relates to property of a relation can not be null.');
        }
        if (null === $this->relatesFromIdentifier->getValue()) {
            throw new MissingPropertyValue('The value of the relates from property of a relation can not be null.');
        }
        $propertyNames = array_map(fn ($property) => $property->getName(), $properties);
        if (count(array_unique($propertyNames)) !== count($properties)) {
            throw new DuplicatePropertiesException('Relation require each property to have an unique name.');
        }
    }

    public function getLabel(): RelationLabel
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

    public function getRelatesToLabel(): NodeLabel
    {
        return $this->relatesToLabel;
    }

    public function getRelatesToIdentifier(): Property
    {
        return $this->relatesToIdentifier;
    }

    public function getRelatesFromLabel(): NodeLabel
    {
        return $this->relatesFromLabel;
    }

    public function getRelatesFromIdentifier(): Property
    {
        return $this->relatesFromIdentifier;
    }
}
