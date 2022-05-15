<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Syndesi\Neo4jSyncBundle\Contract\PaginatedStatementProviderInterface;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;

class DatabaseSyncEvent extends Event
{
    public const NAME = 'neo4j_sync.db.sync';

    /**
     * @param PaginatedStatementProviderInterface[] $paginatedStatementProviders
     */
    public function __construct(
        private array $paginatedStatementProviders = [],
        private CreateType $createType = CreateType::MERGE
    ) {
    }

    public function addPaginatedStatementProvider(PaginatedStatementProviderInterface $paginatedStatementProvider): self
    {
        $this->paginatedStatementProviders[] = $paginatedStatementProvider;

        return $this;
    }

    /**
     * @param PaginatedStatementProviderInterface[] $paginatedStatementProviders
     *
     * @return $this
     */
    public function addPaginatedStatementProviders(array $paginatedStatementProviders): self
    {
        foreach ($paginatedStatementProviders as $paginatedStatementProvider) {
            $this->addPaginatedStatementProvider($paginatedStatementProvider);
        }

        return $this;
    }

    /**
     * @return PaginatedStatementProviderInterface[]
     */
    public function getPaginatedStatementProviders(): array
    {
        return $this->paginatedStatementProviders;
    }

    public function getCreateType(): CreateType
    {
        return $this->createType;
    }
}
