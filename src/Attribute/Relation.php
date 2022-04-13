<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;

#[Attribute]
class Relation
{
    /**
     * @param string|null $label          Label of the relationship
     * @param string|null $targetLabel    Label of the target/parent node
     * @param string|null $targetProperty Name of the target/parent node's id property
     * @param string|null $targetValue    Name of the serialized entities value which represents the targets/parents id
     */
    public function __construct(
        private ?string $label = null,
        private ?string $targetLabel = null,
        private ?string $targetProperty = null,
        private ?string $targetValue = null,
    ) {
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): Relation
    {
        $this->label = $label;

        return $this;
    }

    public function getTargetLabel(): ?string
    {
        return $this->targetLabel;
    }

    public function setTargetLabel(?string $targetLabel): Relation
    {
        $this->targetLabel = $targetLabel;

        return $this;
    }

    public function getTargetProperty(): ?string
    {
        return $this->targetProperty;
    }

    public function setTargetProperty(?string $targetProperty): Relation
    {
        $this->targetProperty = $targetProperty;

        return $this;
    }

    public function getTargetValue(): ?string
    {
        return $this->targetValue;
    }

    public function setTargetValue(?string $targetValue): Relation
    {
        $this->targetValue = $targetValue;

        return $this;
    }
}
