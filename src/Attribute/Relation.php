<?php

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;

#[Attribute]
class Relation
{
    private ?string $label;
    private ?string $targetLabel;
    private ?string $targetProperty;
    private ?string $targetValue;

    /**
     * @param string|null $label          Label of the relationship
     * @param string|null $targetLabel    Label of the target/parent node
     * @param string|null $targetProperty Name of the target/parent node's id property
     * @param string|null $targetValue    Name of the serialized entities value which represents the targets/parents id
     */
    public function __construct(?string $label = null, ?string $targetLabel = null, ?string $targetProperty = null, ?string $targetValue = null)
    {
        $this->label = $label;
        $this->targetLabel = $targetLabel;
        $this->targetProperty = $targetProperty;
        $this->targetValue = $targetValue;
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
