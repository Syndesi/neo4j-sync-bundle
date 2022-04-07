<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class Neo4jClient
{
    private ClientInterface $client;
    /**
     * @var Statement[]
     */
    private array $statements = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function flush(): self
    {
        while ($statement = array_shift($this->statements)) {
            $this->client->runStatement($statement);
        }

        return $this;
    }

    public function clear(): self
    {
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
