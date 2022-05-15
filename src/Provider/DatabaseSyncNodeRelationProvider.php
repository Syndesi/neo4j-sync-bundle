<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\PaginatedStatementProviderInterface;
use Syndesi\Neo4jSyncBundle\Statement\BatchCreateRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\Statement\BatchDeleteRelationsFromNodeStatementBuilder;

class DatabaseSyncNodeRelationProvider implements PaginatedStatementProviderInterface
{
    private int $page = 0;
    private int $size;

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

    public function current(): array
    {
        $elements = $this->em->getRepository($this->className)
            ->createQueryBuilder('n')
            ->setFirstResult($this->page * self::PAGE_SIZE)
            ->setMaxResults(($this->page + 1) * self::PAGE_SIZE)
            ->getQuery()
            ->execute();

        $nodes = [];
        $relationTypes = []; // contains one array per relationship type
        foreach ($elements as $element) {
            $node = $this->nodeAttribute->getNode($element);
            $nodes[] = $node;
            foreach ($node->getRelations() as $relation) {
                $key = $relation->getLabel()->getLabel();
                if (!array_key_exists($key, $relationTypes)) {
                    $relationTypes[$key] = [];
                }
                $relationTypes[$key][] = $relation;
            }
        }
        $relationStatements = [];
        foreach ($relationTypes as $relations) {
            $relationStatements = [
                ...$relationStatements,
                ...BatchCreateRelationStatementBuilder::build($relations),
            ];
        }

        return [
            ...BatchDeleteRelationsFromNodeStatementBuilder::build($nodes),
            ...$relationStatements,
        ];
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
