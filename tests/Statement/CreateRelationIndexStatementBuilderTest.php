<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\CreateRelationIndexStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class CreateRelationIndexStatementBuilderTest extends TestCase
{
    public function testInvalidArgumentBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CreateRelationIndexStatementBuilder::build(
            new Index(
                new IndexName('index'),
                new NodeLabel('DemoNode'),
                [
                    new Property('id'),
                ],
                IndexType::BTREE
            )
        );
    }

    public function testBuild(): void
    {
        $statements = CreateRelationIndexStatementBuilder::build(
            new Index(
                new IndexName('index'),
                new RelationLabel('DEMO_RELATION'),
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
            "CREATE BTREE INDEX index IF NOT EXISTS\n".
            "FOR ()-[relation:DEMO_RELATION]-()\n".
            "ON (relation.id)",
            $statement->getText()
        );
        $this->assertSame(
            [],
            $statement->getParameters()
        );
    }
}
