<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\DeleteAllNodesAndRelationsLimitedStatementBuilder;

class DeleteAllNodesAndRelationsLimitedStatementBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $statements = DeleteAllNodesAndRelationsLimitedStatementBuilder::build();

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertSame(
            "MATCH (n)\n".
            "WITH n LIMIT 10000\n".
            "DETACH DELETE n",
            $statement->getText()
        );
        $this->assertSame(
            [],
            $statement->getParameters()
        );
    }
}
