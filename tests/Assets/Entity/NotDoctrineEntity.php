<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Assets\Entity;

class NotDoctrineEntity {

    private ?int $id;
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

}
