<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\BatchDeleteRelationsFromNodeStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class BatchDeleteRelationsFromNodeStatementBuilderTest extends TestCase
{
    public function testEmptyBuild(): void
    {
        $statements = BatchDeleteRelationsFromNodeStatementBuilder::build([]);
        $this->assertIsArray($statements);
        $this->assertCount(0, $statements);
    }

    public function testInvalidArgumentObjectsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All nodes need to be of type node.');

        BatchDeleteRelationsFromNodeStatementBuilder::build([
            (object) [],
        ]);
    }

    public function testInvalidArgumentLabelsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All nodes need to be for the same node label');

        BatchDeleteRelationsFromNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234),
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('OtherLabel'),
                [
                    new Property('id', 1234),
                ],
                new Property('id')
            ),
        ]);
    }

    public function testBuildWithSingleRelation(): void
    {
        $statements = BatchDeleteRelationsFromNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234),
                ],
                new Property('id')
            ),
        ]);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "UNWIND \$batch as row\n".
            "MATCH\n".
            "  (child:SomeLabel {id: row.id})\n".
            "  -[relation]->\n".
            "  (parent)\n".
            "DELETE relation",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 1234,
                    ],
                ],
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithMultipleRelations(): void
    {
        $statements = BatchDeleteRelationsFromNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234),
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1235),
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1236),
                ],
                new Property('id')
            ),
        ]);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "UNWIND \$batch as row\n".
            "MATCH\n".
            "  (child:SomeLabel {id: row.id})\n".
            "  -[relation]->\n".
            "  (parent)\n".
            "DELETE relation",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 1234,
                    ],
                    [
                        'id' => 1235,
                    ],
                    [
                        'id' => 1236,
                    ],
                ],
            ],
            $statement->getParameters()
        );
    }
}
