<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\CreateOrUpdateNodeWithRelationsStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class CreateOrUpdateNodeWithRelationsStatementBuilderTest extends TestCase
{
    public function testBuildWithoutRelations(): void
    {
        $statements = CreateOrUpdateNodeWithRelationsStatementBuilder::build(
            new Node(
                new NodeLabel('DemoNode'),
                [
                    new Property('id', 1234),
                    new Property('string', 'Hello World'),
                    new Property('float', 1.23),
                ],
                new Property('id')
            )
        );

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "MERGE (node:DemoNode {id: \$id})\n".
            "ON CREATE\n".
            "  SET\n".
            "    node.string = \$string,\n".
            "    node.float = \$float\n".
            "ON MATCH\n".
            "  SET\n".
            "    node.string = \$string,\n".
            "    node.float = \$float",
            $statement->getText()
        );
        $this->assertSame(
            [
                'id' => 1234,
                'string' => 'Hello World',
                'float' => 1.23,
            ],
            $statement->getParameters()
        );
    }

    public function testBuildWithSimpleRelations(): void
    {
        $statements = CreateOrUpdateNodeWithRelationsStatementBuilder::build(
            new Node(
                new NodeLabel('DemoNode'),
                [
                    new Property('id', 1234),
                    new Property('string', 'Hello World'),
                    new Property('float', 1.23),
                ],
                new Property('id'),
                [
                    new Relation(
                        new RelationLabel('DEMO_RELATION'),
                        new NodeLabel('ParentNode'),
                        new Property('parentNodeId', 4321),
                        new NodeLabel('DemoNode'),
                        new Property('id', 1234),
                        [
//                            new Property('relationId', 123),
                            new Property('someKey', 'some value'),
                        ],
//                        new Property('relationId')
                    ),
                ]
            )
        );

        $this->assertIsArray($statements);
        $this->assertCount(3, $statements);
        $deleteRelationsStatement = $statements[1];

        $this->assertSame(
            "MATCH\n".
            "  (child:DemoNode {id: \$id})\n".
            "  -[relation {_managedBy: \$_managedBy}]->\n".
            "  (parent)\n".
            "DELETE relation",
            $deleteRelationsStatement->getText()
        );
        $this->assertSame(
            [
                'id' => 1234,
                '_managedBy' => 'DemoNode',
            ],
            $deleteRelationsStatement->getParameters()
        );

        $createRelationStatement = $statements[2];

        $this->assertSame(
            "MATCH\n".
            "  (child:DemoNode {id: \$_childId}),\n".
            "  (parent:ParentNode {parentNodeId: \$_parentId})\n".
            "CREATE (child)-[:DEMO_RELATION {someKey: \$someKey}]->(parent)",
            $createRelationStatement->getText()
        );
        $this->assertSame(
            [
                'someKey' => 'some value',
                '_parentId' => 4321,
                '_childId' => 1234,
            ],
            $createRelationStatement->getParameters()
        );
    }
}
