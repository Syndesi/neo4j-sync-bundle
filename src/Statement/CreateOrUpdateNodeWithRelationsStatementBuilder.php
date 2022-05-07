<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Exception;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class CreateOrUpdateNodeWithRelationsStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * @throws Exception
     */
    public static function build(Node $node): array
    {
        $statements = [
            ...MergeNodeStatementBuilder::build($node)
        ];
        if (!empty($node->getRelations())) {
            if ($node->areAllRelationsIdentifiable()) {
                // because all relations have an identifier, they can be created/updated with the MERGE clause
                throw new Exception('not implemented yet');
            } else {
                // at least one relation does not have an identifier, all outgoing relations need to be removed and
                // recreated
                $statements = [
                    ...$statements,
                    ...DeleteRelationsFromNodeStatementBuilder::build($node)
                ];
                foreach ($node->getRelations() as $relation) {
                    $tmpRelation = new Relation(
                        $relation->getLabel(),
                        $relation->getRelatesToLabel(),
                        new Property($relation->getRelatesToIdentifier()->getName(), $node->getProperty($relation->getRelatesToIdentifier()->getValue())),
                        $node->getLabel(),
                        new Property($node->getIdentifier()->getName(), $node->getProperty($node->getIdentifier()->getName())),
                        $relation->getProperties(),
                        $relation->getIdentifier()
                    );
                    $statements = [
                        ...$statements,
                        ...CreateRelationStatementBuilder::build($tmpRelation)
                    ];
                }
            }
        }
        return $statements;
    }
}
