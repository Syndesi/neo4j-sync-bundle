<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Syndesi\Neo4jSyncBundle\Attribute as Neo4jSync;
use Symfony\Component\Serializer\Annotation\Groups;

#[Neo4jSync\Node(label:"SimpleEntity", id:"id", serializationGroup:"neo4j")]
#[ORM\Entity]
class SimpleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('neo4j')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('neo4j')]
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
