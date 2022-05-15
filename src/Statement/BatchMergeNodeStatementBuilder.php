<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\BatchNodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class BatchMergeNodeStatementBuilder implements BatchNodeStatementBuilderInterface
{
    /**
     * @param Node[] $nodes Important: All nodes need to be of the same type
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     * @throws InvalidArgumentException
     */
    public static function build(array $nodes): array
    {
        if (empty($nodes)) {
            return [];
        }
        foreach ($nodes as $node) {
            if (!($node instanceof Node)) {
                throw new InvalidArgumentException('All nodes need to be of type node.');
            }
            if (!$node->getLabel()->isEqualTo($nodes[0]->getLabel())) {
                throw new InvalidArgumentException('All nodes need to be for the same node label');
            }
        }
        $batch = [];
        foreach ($nodes as $node) {
            $properties = [];
            foreach ($node->getProperties() as $property) {
                if ($property->getName() === $node->getIdentifier()->getName()) {
                    // id is not a property, it cannot be changed once set
                    continue;
                }
                $properties[$property->getName()] = $property->getValue();
            }
            $batch[] = [
                'id' => $node->getIdentifier()->getValue(),
                'properties' => $properties,
            ];
        }

        return [new Statement(
            sprintf(
                "UNWIND \$batch as row\n".
                "MERGE (n:%s {%s: row.id})\n".
                "SET n += row.properties",
                $nodes[0]->getLabel(),
                $nodes[0]->getIdentifier()->getName()
            ),
            [
                'batch' => $batch,
            ]
        )];
    }
}
