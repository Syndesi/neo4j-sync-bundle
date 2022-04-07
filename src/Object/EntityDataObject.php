<?php

namespace Syndesi\Neo4jSyncBundle\Object;

use Syndesi\Neo4jSyncBundle\Attribute\Node;

class EntityDataObject
{
    private ?string $entityClass = null;
    private ?Node $nodeAttribute = null;
    private array $data = [];
    private array $properties = [];

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function setEntityClass(?string $entityClass): EntityDataObject
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getNodeAttribute(): ?Node
    {
        return $this->nodeAttribute;
    }

    public function setNodeAttribute(?Node $nodeAttribute): EntityDataObject
    {
        $this->nodeAttribute = $nodeAttribute;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): EntityDataObject
    {
        $this->data = $data;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): EntityDataObject
    {
        $this->properties = $properties;

        return $this;
    }
}
