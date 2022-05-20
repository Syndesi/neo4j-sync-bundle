<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Statement\DeleteIndexStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class DeleteIndexStatementBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $statements = DeleteIndexStatementBuilder::build(
            new Index(
                new IndexName('index'),
                new NodeLabel('DemoNode'),
                [
                    new Property('id'),
                ],
                IndexType::BTREE
            )
        );

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertSame(
            "DROP INDEX index",
            $statement->getText()
        );
        $this->assertSame(
            [],
            $statement->getParameters()
        );
    }
}
