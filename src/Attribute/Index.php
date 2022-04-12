<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\IndexType;

#[Attribute]
class Index
{
    private ?string $name;
    private ?IndexType $type;
    /**
     * @var string[]
     */
    private array $fields;

    public function __construct(string $name, IndexType $type, array $fields)
    {
        $this->name = $name;
        $this->type = $type;
        $this->fields = $fields;
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

    public function getType(): ?IndexType
    {
        return $this->type;
    }

    public function setType(?IndexType $type): self
    {
        $this->type = $type;

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
