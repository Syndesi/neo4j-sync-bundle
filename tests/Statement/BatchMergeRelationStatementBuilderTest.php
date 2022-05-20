<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\BatchMergeRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class BatchMergeRelationStatementBuilderTest extends TestCase {

    public function testEmptyBuild(): void
    {
        $statements = BatchMergeRelationStatementBuilder::build([]);
        $this->assertIsArray($statements);
        $this->assertCount(0, $statements);
    }

    public function testInvalidArgumentObjectsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All relations need to be of type relation.');

        BatchMergeRelationStatementBuilder::build([
            (object)[]
        ]);
    }

    public function testInvalidArgumentLabelsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All relations need to be for the same relation label');

        BatchMergeRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321)
            ),
            new Relation(
                new RelationLabel('OTHER_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321)
            ),
        ]);
    }

    public function testInvalidArgumentIdentifierBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All relations need to contain an identifier.');

        BatchMergeRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321)
            )
        ]);
    }

    public function testBuildWithSingleRelationMerge(): void
    {

        $statements = BatchMergeRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321),
                [
                    new Property('id', 123),
                    new Property('someKey', 'someValue')
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
            "  (child:ChildLabel {childId: row.childId}),\n".
            "  (parent:ParentLabel {parentId: row.parentId})\n".
            "MERGE (child)-[relation:SOME_LABEL {id: row.id}]->(parent)\n".
            "SET relation += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 123,
                        'childId' => 4321,
                        'parentId' => 1234,
                        'properties' => [
                            'someKey' => 'someValue'
                        ]
                    ]
                ]
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithSingleRelationCreate(): void
    {

        $statements = BatchMergeRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321),
                [
                    new Property('id', 123),
                    new Property('someKey', 'someValue')
                ],
                new Property('id')
            ),
        ], CreateType::CREATE);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "UNWIND \$batch as row\n".
            "MATCH\n".
            "  (child:ChildLabel {childId: row.childId}),\n".
            "  (parent:ParentLabel {parentId: row.parentId})\n".
            "CREATE (child)-[relation:SOME_LABEL {id: row.id}]->(parent)\n".
            "SET relation += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 123,
                        'childId' => 4321,
                        'parentId' => 1234,
                        'properties' => [
                            'someKey' => 'someValue'
                        ]
                    ]
                ]
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithMultipleRelations(): void
    {

        $statements = BatchMergeRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321),
                [
                    new Property('id', 123),
                    new Property('someKey', 'someValue')
                ],
                new Property('id')
            ),
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1235),
                new NodeLabel('ChildLabel'),
                new Property('childId', 5321),
                [
                    new Property('id', 124),
                    new Property('someKey', 'someValue')
                ],
                new Property('id')
            ),
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1236),
                new NodeLabel('ChildLabel'),
                new Property('childId', 6321),
                [
                    new Property('id', 125)
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
            "  (child:ChildLabel {childId: row.childId}),\n".
            "  (parent:ParentLabel {parentId: row.parentId})\n".
            "MERGE (child)-[relation:SOME_LABEL {id: row.id}]->(parent)\n".
            "SET relation += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'id' => 123,
                        'childId' => 4321,
                        'parentId' => 1234,
                        'properties' => [
                            'someKey' => 'someValue'
                        ]
                    ],
                    [
                        'id' => 124,
                        'childId' => 5321,
                        'parentId' => 1235,
                        'properties' => [
                            'someKey' => 'someValue'
                        ]
                    ],
                    [
                        'id' => 125,
                        'childId' => 6321,
                        'parentId' => 1236,
                        'properties' => []
                    ],
                ]
            ],
            $statement->getParameters()
        );
    }

}
