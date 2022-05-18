<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;
use Syndesi\Neo4jSyncBundle\Contract\LabelInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;

class Index implements Stringable, IsEqualToInterface
{
    /**
     * @param IndexName      $name
     * @param LabelInterface $label
     * @param Property[]     $properties
     * @param IndexType      $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly IndexName $name,
        private readonly LabelInterface $label,
        private readonly array $properties,
        private readonly IndexType $type = IndexType::BTREE
    ) {
        if (empty($this->properties)) {
            throw new InvalidArgumentException("Index requires at least one property to be set");
        }
        if (!($label instanceof NodeLabel) && !($label instanceof RelationLabel)) {
            throw new InvalidArgumentException("Index only supports nodes and relations");
        }
    }

    public function getName(): IndexName
    {
        return $this->name;
    }

    public function getLabel(): LabelInterface
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

    public function getType(): IndexType
    {
        return $this->type;
    }

    public function __toString()
    {
        if ($this->label instanceof NodeLabel) {
            $typeString = 'node';
            $nameString = sprintf("(%s:%s)", $typeString, $this->label->getLabel());
        } else {
            $typeString = 'relation';
            $nameString = sprintf("()-[%s:%s]-()", $typeString, $this->label->getLabel());
        }
        $propertyString = [];
        foreach ($this->properties as $property) {
            $propertyString[] = sprintf("%s.%s", $typeString, $property->getName());
        }
        $propertyString = implode(', ', $propertyString);

        return sprintf(
            "%s INDEX %s FOR %s ON (%s)",
            $this->type->value,
            (string) $this->name,
            $nameString,
            $propertyString
        );
    }

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof Index)) {
            return false;
        }

        $arePropertiesEqual = true;
        if (count($this->properties) !== count($element->properties)) {
            $arePropertiesEqual = false;
        } else {
            foreach ($this->properties as $i => $property) {
                if (!$property->isEqualTo($element->properties[$i])) {
                    $arePropertiesEqual = false;
                    break;
                }
            }
        }

        return
            $this->name->isEqualTo($element->name) &&
            $this->label->isEqualTo($element->label) &&
            $arePropertiesEqual &&
            $this->type->value === $element->type->value;
    }
}
