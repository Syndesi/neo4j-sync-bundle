<?php
namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Node
{
    private ?string $label = null;
    private ?string $id = null;
    private ?string $serializationGroup = null;

    public function __construct(string $label, string $id, string $serializationGroup = 'neo4j')
    {
        $this->label = $label;
        $this->id = $id;
        $this->serializationGroup = $serializationGroup;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): Node
    {
        $this->label = $label;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): Node
    {
        $this->id = $id;
        return $this;
    }

    public function getSerializationGroup(): ?string
    {
        return $this->serializationGroup;
    }

    public function setSerializationGroup(?string $serializationGroup): Node
    {
        $this->serializationGroup = $serializationGroup;
        return $this;
    }

}
