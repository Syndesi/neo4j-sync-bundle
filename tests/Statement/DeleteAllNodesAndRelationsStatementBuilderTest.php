<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\DeleteAllNodesAndRelationsStatementBuilder;

class DeleteAllNodesAndRelationsStatementBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $statements = DeleteAllNodesAndRelationsStatementBuilder::build();

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertSame(
            "MATCH (n)\n".
            "DETACH DELETE n",
            $statement->getText()
        );
        $this->assertSame(
            [],
            $statement->getParameters()
        );
    }
}
