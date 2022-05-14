<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\PaginatedStatementProviderInterface;
use Syndesi\Neo4jSyncBundle\Statement\MergeNodeStatementBuilder;

class DatabaseSyncNodeProvider implements PaginatedStatementProviderInterface
{
    private int $page = 0;
    private int $size = 0;

    public function __construct(
        private string $className,
        private EntityManagerInterface $em,
        private NodeAttributeInterface $nodeAttribute
    ) {
        $this->size = $this->em->getRepository($this->className)->count([]);
    }

    public function next(): void
    {
        ++$this->page;
    }

    public function key(): mixed
    {
        return $this->page;
    }

    public function valid(): bool
    {
        return ($this->page * self::PAGE_SIZE) < $this->size;
    }

    public function rewind(): void
    {
        $this->page = 0;
    }

    /**
     * @return Statement[]
     */
    public function current(): array
    {
        $elements = $this->em->getRepository($this->className)
            ->createQueryBuilder('n')
            ->setFirstResult($this->page * self::PAGE_SIZE)
            ->setMaxResults(($this->page + 1) * self::PAGE_SIZE - 1)
            ->getQuery()
            ->execute();
        $statements = [];
        foreach ($elements as $element) {
            $node = $this->nodeAttribute->getNode($element);
            $statements = [
                ...$statements,
                ...MergeNodeStatementBuilder::build($node),
            ];
        }

        return $statements;
    }
}
