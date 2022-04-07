<?php

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;

#[Attribute]
class Index
{
    private ?string $type;
    private ?string $name;
    /** @var string[] */
    private array $fields;

    public function __construct(string $type, string $name, array $fields)
    {
        $this->type = $type;
        $this->name = $name;
        $this->fields = $fields;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string[] $fields
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
