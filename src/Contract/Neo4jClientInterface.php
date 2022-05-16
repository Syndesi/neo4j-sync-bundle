<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;
use Psr\Log\LoggerInterface;

interface Neo4jClientInterface
{
    public function __construct(ClientInterface $client, LoggerInterface $logger);

    public function flush(bool $concurrently = false): self;

    public function clear(): self;

    public function getClient(): ClientInterface;

    public function setClient(ClientInterface $client): self;

    /**
     * @return Statement[]
     */
    public function getStatements(): array;

    /**
     * @param Statement[] $statements
     */
    public function setStatements(array $statements): self;

    public function addStatement(Statement $statement): self;

    /**
     * @param Statement[] $statements
     */
    public function addStatements(array $statements): self;

    public function runStatement(Statement $statement): mixed;
}
