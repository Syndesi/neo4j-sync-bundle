<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\GetIndicesStatementBuilder;

class GetIndicesStatementBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $statements = GetIndicesStatementBuilder::build();

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertSame(
            "SHOW INDEX",
            $statement->getText()
        );
        $this->assertSame(
            [],
            $statement->getParameters()
        );
    }
}
