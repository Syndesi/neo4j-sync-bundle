<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;

class GetAllIndicesEvent extends Event
{
    public const NAME = 'neo4j_sync.index.get_all';

    /**
     * @param Index[] $indices
     */
    public function __construct(
        private array $indices = []
    ) {
    }

    public function addIndex(Index $index): self
    {
        $this->indices[] = $index;

        return $this;
    }

    /**
     * @param Index[] $indices
     *
     * @return $this
     */
    public function addIndices(array $indices): self
    {
        foreach ($indices as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    /**
     * @return Index[]
     */
    public function getIndices(): array
    {
        return $this->indices;
    }
}
