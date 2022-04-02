<?php
namespace Syndesi\Neo4jSyncBundle\Object;

class RelationObject {

    private ?string $label = null;
    private ?string $targetLabel = null;
    private ?string $targetProperty = null;
    private ?string $targetValue = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): RelationObject
    {
        $this->label = $label;
        return $this;
    }

    public function getTargetLabel(): ?string
    {
        return $this->targetLabel;
    }

    public function setTargetLabel(?string $targetLabel): RelationObject
    {
        $this->targetLabel = $targetLabel;
        return $this;
    }

    public function getTargetProperty(): ?string
    {
        return $this->targetProperty;
    }

    public function setTargetProperty(?string $targetProperty): RelationObject
    {
        $this->targetProperty = $targetProperty;
        return $this;
    }

    public function getTargetValue(): ?string
    {
        return $this->targetValue;
    }

    public function setTargetValue(?string $targetValue): RelationObject
    {
        $this->targetValue = $targetValue;
        return $this;
    }

}
