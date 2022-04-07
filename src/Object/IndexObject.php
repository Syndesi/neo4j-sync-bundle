<?php

namespace Syndesi\Neo4jSyncBundle\Object;

class IndexObject
{
    private ?string $label = null;
    private ?string $idProperty = null;
    private ?string $idValue = null;
    private array $properties = [];
    /** @var RelationObject */
    private array $relations = [];
}
