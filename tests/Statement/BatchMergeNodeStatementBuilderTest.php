<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\BatchMergeNodeStatementBuilder;
use Syndesi\Neo4jSyncBundle\Statement\CreateRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class BatchMergeNodeStatementBuilderTest extends TestCase {

    public function testEmptyBuild(): void
    {
        $statements = BatchMergeNodeStatementBuilder::build([]);
        $this->assertIsArray($statements);
        $this->assertCount(0, $statements);
    }

    public function testInvalidArgumentObjectsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All nodes need to be of type node.');

        BatchMergeNodeStatementBuilder::build([
            (object)[]
        ]);
    }

    public function testInvalidArgumentLabelsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All nodes need to be for the same node label');

        BatchMergeNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234)
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('OtherLabel'),
                [
                    new Property('id', 1234)
                ],
                new Property('id')
            ),
        ]);
    }

    public function testBuildWithSingleNode(): void
    {

        $statements = BatchMergeNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234),
                    new Property('string', 'some value (1)'),
                    new Property('int', 1)
                ],
                new Property('id')
            )
        ]);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "UNWIND \$batch as row\n".
            "MERGE (n:SomeLabel {id: row.id})\n".
            "SET n += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 1234,
                        'properties' => [
                            'string' => 'some value (1)',
                            'int' => 1
                        ]
                    ]
                ]
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithMultiplenodes(): void
    {

        $statements = BatchMergeNodeStatementBuilder::build([
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1234),
                    new Property('string', 'some value (1)'),
                    new Property('int', 1)
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1235),
                    new Property('string', 'some value (2)'),
                    new Property('int', 2)
                ],
                new Property('id')
            ),
            new Node(
                new NodeLabel('SomeLabel'),
                [
                    new Property('id', 1236),
                    new Property('string', 'some value (3)'),
                    new Property('int', 3)
                ],
                new Property('id')
            ),
        ]);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "UNWIND \$batch as row\n".
            "MERGE (n:SomeLabel {id: row.id})\n".
            "SET n += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 1234,
                        'properties' => [
                            'string' => 'some value (1)',
                            'int' => 1
                        ]
                    ],
                    [
                        'id' => 1235,
                        'properties' => [
                            'string' => 'some value (2)',
                            'int' => 2
                        ]
                    ],
                    [
                        'id' => 1236,
                        'properties' => [
                            'string' => 'some value (3)',
                            'int' => 3
                        ]
                    ],
                ]
            ],
            $statement->getParameters()
        );
    }

}
