<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class NotNeo4jEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('someGroup')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('someGroup')]
    private ?string $text;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}
