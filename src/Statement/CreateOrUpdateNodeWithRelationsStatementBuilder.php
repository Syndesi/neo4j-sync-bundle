<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Exception;
use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\NodeStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;

class CreateOrUpdateNodeWithRelationsStatementBuilder implements NodeStatementBuilderInterface
{
    /**
     * Returns statements for creating/updating a node with all its relationships.
     *
     * @return Statement[]
     *
     * @throws Exception
     */
    public static function build(Node $node): array
    {
        $statements = [
            ...MergeNodeStatementBuilder::build($node),
        ];
        if (!empty($node->getRelations())) {
            if ($node->areAllRelationsIdentifiable()) {
                // because all relations have an identifier, they can be created/updated with the MERGE clause
                // todo
                throw new Exception('not implemented yet');
            } else {
                // at least one relation does not have an identifier, all outgoing relations need to be removed and
                // recreated
                $statements = [
                    ...$statements,
                    ...DeleteRelationsFromNodeStatementBuilder::build($node),
                ];
                foreach ($node->getRelations() as $relation) {
                    $statements = [
                        ...$statements,
                        ...CreateRelationStatementBuilder::build($relation),
                    ];
                }
            }
        }

        return $statements;
    }
}
