<?php

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Node
{
    private ?string $label;
    private ?string $id;
    private ?string $serializationGroup;
    /** @var Relation[] */
    private array $relations;
    /** @var Index[] */
    private array $indices;

    /**
     * @param string $label              Label of the Neo4j node. Camel-case, beginning with an upper-case character, e.g. "VehicleOwner".
     * @param string $id                 Key of the serialized property which acts as the primary identifier, usually "id". Must be unique for the Neo4j node label.
     * @param string $serializationGroup group which is used by the serializer to normalize the entity, usually "neo4j"
     * @param array  $relations          Array of relation attributes. **Note**: Only use relations on the owning side, see readme.
     * @param array  $indices            Array of index attributes. **Note**: By default no index is created, please create at least one for the identifier.
     */
    public function __construct(string $label, string $id, string $serializationGroup = 'neo4j', array $relations = [], array $indices = [])
    {
        $this->label = $label;
        $this->id = $id;
        $this->serializationGroup = $serializationGroup;
        $this->relations = $relations;
        $this->indices = $indices;
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

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param Relation[] $relations
     */
    public function setRelations(array $relations): Node
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @return Index[]
     */
    public function getIndices(): array
    {
        return $this->indices;
    }

    /**
     * @param Index[] $indices
     */
    public function setIndices(array $indices): self
    {
        $this->indices = $indices;

        return $this;
    }
}
