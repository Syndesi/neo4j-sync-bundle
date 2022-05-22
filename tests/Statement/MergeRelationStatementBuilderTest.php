<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Statement\MergeRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class MergeRelationStatementBuilderTest extends TestCase
{
    public function testInvalidArgumentBuild(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MergeRelationStatementBuilder::build(
            new Relation(
                new RelationLabel('DEMO_RELATION'),
                new NodeLabel('ParentNode'),
                new Property('parentNodeId', 1234),
                new NodeLabel('ChildNode'),
                new Property('childNodeId', 4321),
                [
                    new Property('relationId', 123),
                    new Property('someKey', 'some value'),
                ],
            )
        );
    }

    public function testBuild(): void
    {
        $statements = MergeRelationStatementBuilder::build(
            new Relation(
                new RelationLabel('DEMO_RELATION'),
                new NodeLabel('ParentNode'),
                new Property('parentNodeId', 1234),
                new NodeLabel('ChildNode'),
                new Property('childNodeId', 4321),
                [
                    new Property('relationId', 123),
                    new Property('someKey', 'some value'),
                ],
                new Property('relationId')
            )
        );

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];

        $this->assertSame(
            "MATCH\n".
            "  (child:ChildNode {childNodeId: \$_childId}),\n".
            "  (parent:ParentNode {parentNodeId: \$_parentId})\n".
            "MERGE (child)-[relation:DEMO_RELATION {relationId: \$relationId}]->(parent)\n".
            "ON CREATE\n".
            "  SET\n".
            "    relation.someKey = \$someKey\n".
            "ON MATCH\n".
            "  SET\n".
            "    relation.someKey = \$someKey",
            $statement->getText()
        );
        $this->assertSame(
            [
                'relationId' => 123,
                'someKey' => 'some value',
                '_childId' => 4321,
                '_parentId' => 1234,
            ],
            $statement->getParameters()
        );
    }
}
