<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;
use Psr\Log\LoggerInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class Neo4jClient implements Neo4jClientInterface
{
    /**
     * @var Statement[]
     */
    private array $statements = [];

    public function __construct(
        private ClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }

    public function flush(bool $concurrently = false): self
    {
        $this->logger->debug('START FLUSHING');
        if ($concurrently) {
            $this->client->runStatements($this->statements);
            $this->statements = [];
        } else {
            while ($statement = array_shift($this->statements)) {
                $this->logger->debug($statement->getText(), $statement->getParameters());
                $this->client->runStatement($statement);
            }
        }
        $this->logger->debug('FINISHED FLUSHING');

        return $this;
    }

    public function clear(): self
    {
        $this->logger->debug('CLEARED STATEMENTS');
        $this->statements = [];

        return $this;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Statement[]
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    /**
     * @param Statement[] $statements
     */
    public function setStatements(array $statements): self
    {
        $this->statements = $statements;

        return $this;
    }

    public function addStatement(Statement $statement): self
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * @param Statement[] $statements
     */
    public function addStatements(array $statements): self
    {
        $this->statements = array_merge($this->statements, $statements);

        return $this;
    }
}
