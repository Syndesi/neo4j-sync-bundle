<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\RelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;

class DeleteRelationStatementBuilder implements RelationStatementBuilderInterface
{
    /**
     * Returns a statement which deletes a single relation. Relation must have an identifier.
     *
     * @return Statement[]
     *
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public static function build(Relation $relation): array
    {
        return [new Statement(
            sprintf(
                "MATCH\n".
                "  ()-[relation:%s {%s: $%s}]->()\n".
                "DELETE relation",
                $relation->getLabel(),
                $relation->getIdentifier()->getName(),
                $relation->getIdentifier()->getName()
            ),
            [
                $relation->getIdentifier()->getName() => $relation->getIdentifier()->getValue(),
            ]
        )];
    }
}
