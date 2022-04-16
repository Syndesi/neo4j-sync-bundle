<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;

class MergeNodeStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * @param Property[] $properties
     */
    private function getPropertiesStringFromProperties(array $properties): string
    {
        $propertiesString = [];
        foreach ($properties as $property) {
            $propertiesString[] = sprintf('%s: %s', $property->getName(), $property->getName());
        }

        return implode(', ', $propertiesString);
    }

    /**
     * @param Property[] $properties
     */
    private function getAssociativeArrayFromProperties(array $properties): array
    {
        $associativeArray = [];
        foreach ($properties as $property) {
            $associativeArray[$property->getName()] = $property->getValue();
        }

        return $associativeArray;
    }

    public function getStatements(Node $node): array
    {
        return [
            new Statement(
                sprintf(
                    'MERGE (n:%s {%s})',
                    $node->getLabel(),
                    $this->getPropertiesStringFromProperties($node->getProperties())
                ),
                $this->getAssociativeArrayFromProperties($node->getProperties())
            ),
        ];
    }
}
