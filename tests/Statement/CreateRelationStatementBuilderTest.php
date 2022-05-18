<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\CreateRelationStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class CreateRelationStatementBuilderTest extends TestCase {

    public function testBuild(): void
    {
        $relation = new Relation(
            new RelationLabel('DEMO_RELATION'),
            new NodeLabel('ParentNode'),
            new Property('parentNodeId', 1234),
            new NodeLabel('ChildNode'),
            new Property('childNodeId', 4321),
            [
                new Property('relationId', 123),
                new Property('someKey', 'some value')
            ],
            new Property('relationId')
        );

        $statements = CreateRelationStatementBuilder::build($relation);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);
        $statement = $statements[0];

        $this->assertSame(
            "MATCH\n".
            "  (child:ChildNode {childNodeId: \$_childId}),\n".
            "  (parent:ParentNode {parentNodeId: \$_parentId})\n".
            "CREATE (child)-[:DEMO_RELATION {relationId: \$relationId, someKey: \$someKey}]->(parent)",
            $statement->getText()
        );
        $this->assertSame(
            [
                'relationId' => 123,
                'someKey' => 'some value',
                '_parentId' => 1234,
                '_childId' => 4321
            ],
            $statement->getParameters()
        );
    }

}
