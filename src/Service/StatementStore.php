<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Databags\Statement;

class StatementStore
{
    /**
     * @var Statement[]
     */
    private array $nodeStatements = [];

    /**
     * @var Statement[]
     */
    private array $relationStatements = [];

    /**
     * @return Statement[]
     */
    public function getNodeStatements(): array
    {
        return $this->nodeStatements;
    }

    public function addNodeStatement(Statement $statement): self
    {
        $this->nodeStatements[] = $statement;

        return $this;
    }

    /**
     * @param Statement[] $statements
     */
    public function addNodeStatements(array $statements): self
    {
        $this->nodeStatements = array_merge($this->nodeStatements, $statements);

        return $this;
    }

    public function clearNodeStatements(): self
    {
        $this->nodeStatements = [];

        return $this;
    }

    /**
     * @return Statement[]
     */
    public function getRelationStatements(): array
    {
        return $this->relationStatements;
    }

    public function addRelationStatement(Statement $statement): self
    {
        $this->relationStatements[] = $statement;

        return $this;
    }

    /**
     * @param Statement[] $statements
     */
    public function addRelationStatements(array $statements): self
    {
        $this->relationStatements = array_merge($this->relationStatements, $statements);

        return $this;
    }

    public function clearRelationStatements(): self
    {
        $this->relationStatements = [];

        return $this;
    }
}
