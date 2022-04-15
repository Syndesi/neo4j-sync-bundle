<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Object;

use Syndesi\Neo4jSyncBundle\Attribute\Node;

class EntityObject
{
    private ?string $entityClass = null;
    private ?Node $nodeAttribute = null;
    private array $data = [];
    private array $properties = [];

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function setEntityClass(?string $entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getNodeAttribute(): ?Node
    {
        return $this->nodeAttribute;
    }

    public function setNodeAttribute(?Node $nodeAttribute): self
    {
        $this->nodeAttribute = $nodeAttribute;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }
}
