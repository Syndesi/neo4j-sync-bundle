<?php
namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Object\RelationObject;

#[Attribute(Attribute::TARGET_CLASS)]
class Relation
{
    /** @var array RelationObject[] */
    private array $relations = [];

    public function __construct(array $relations)
    {
        $this->relations = $relations;
    }

    /**
     * @return RelationObject[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param RelationObject[] $relations
     */
    public function setRelations(array $relations): Relation
    {
        $this->relations = $relations;
        return $this;
    }

}
