<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\PaginatedStatementProviderInterface;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Statement\BatchCreateNodeStatementBuilder;
use Syndesi\Neo4jSyncBundle\Statement\BatchMergeNodeStatementBuilder;

class DatabaseSyncNodeProvider implements PaginatedStatementProviderInterface
{
    private int $page = 0;
    private int $size;

    /**
     * @param class-string $className
     */
    public function __construct(
        private string $className,
        private EntityManagerInterface $em,
        private NodeAttributeInterface $nodeAttribute,
        private CreateType $createType = CreateType::MERGE
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
        /** @var class-string $className */
        $className = $this->className;
        $elements = $this->em->getRepository($this->className)
            ->createQueryBuilder('n')
            ->setFirstResult($this->page * self::PAGE_SIZE)
            ->setMaxResults(($this->page + 1) * self::PAGE_SIZE)
            ->getQuery()
            ->execute();
        $nodes = [];
        foreach ($elements as $element) {
            $nodes[] = $this->nodeAttribute->getNode($element);
        }

        if (CreateType::MERGE === $this->createType) {
            return BatchMergeNodeStatementBuilder::build($nodes);
        } else {
            return BatchCreateNodeStatementBuilder::build($nodes);
        }
    }

    public function countPages(): int
    {
        return (int) ceil((float) $this->size / (float) self::PAGE_SIZE);
    }

    public function countElements(): int
    {
        return $this->size;
    }

    public function getName(): string
    {
        return $this->className;
    }
}
