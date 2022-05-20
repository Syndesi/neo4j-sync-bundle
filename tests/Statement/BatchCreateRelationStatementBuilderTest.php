<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\BatchCreateRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class BatchCreateRelationStatementBuilderTest extends TestCase {

    public function testEmptyBuild(): void
    {
        $statements = BatchCreateRelationStatementBuilder::build([]);
        $this->assertIsArray($statements);
        $this->assertCount(0, $statements);
    }

    public function testInvalidArgumentObjectsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All relations need to be of type relation.');

        BatchCreateRelationStatementBuilder::build([
            (object)[]
        ]);
    }

    public function testInvalidArgumentLabelsBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All relations need to be for the same relation label');

        BatchCreateRelationStatementBuilder::build([
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

    public function testBuildWithSingleRelation(): void
    {

        $statements = BatchCreateRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321)
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
            "CREATE (child)-[relation:SOME_LABEL]->(parent)\n".
            "SET relation += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'childId' => 4321,
                        'parentId' => 1234,
                        'properties' => [
                            '_' => null
                        ]
                    ]
                ]
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithMultipleRelations(): void
    {

        $statements = BatchCreateRelationStatementBuilder::build([
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1234),
                new NodeLabel('ChildLabel'),
                new Property('childId', 4321)
            ),
            new Relation(
                new RelationLabel('SOME_LABEL'),
                new NodeLabel('ParentLabel'),
                new Property('parentId', 1235),
                new NodeLabel('ChildLabel'),
                new Property('childId', 5321),
                [
                    new Property('id', 123),
                    new Property('someData', 'Hello world! :D')
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
            "CREATE (child)-[relation:SOME_LABEL]->(parent)\n".
            "SET relation += row.properties",
            $statement->getText()
        );
        $this->assertSame(
            [
                'batch' => [
                    [
                        'childId' => 4321,
                        'parentId' => 1234,
                        'properties' => [
                            '_' => null
                        ]
                    ],
                    [
                        'childId' => 5321,
                        'parentId' => 1235,
                        'properties' => [
                            '_' => null,
                            'id' => 123,
                            'someData' => 'Hello world! :D'
                        ]
                    ]
                ]
            ],
            $statement->getParameters()
        );
    }

}
