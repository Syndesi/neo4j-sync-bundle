<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Statement;

use Laudis\Neo4j\Databags\Statement;
use Syndesi\Neo4jSyncBundle\Contract\RelationStatementBuilderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
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
     * @throws InvalidArgumentException
     */
    public static function build(Relation $relation): array
    {
        $identifier = $relation->getIdentifier();
        if (!$identifier) {
            throw new InvalidArgumentException("Relation must contain identifier.");
        }

        return [new Statement(
            sprintf(
                "MATCH\n".
                "  ()-[relation:%s {%s: $%s}]->()\n".
                "DELETE relation",
                (string) $relation->getLabel(),
                $identifier->getName(),
                $identifier->getName()
            ),
            [
                $identifier->getName() => $identifier->getValue(),
            ]
        )];
    }
}
