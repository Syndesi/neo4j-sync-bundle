<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\DeleteRelationsFromNodeStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class DeleteRelationsFromNodeStatementBuilderTest extends TestCase {

    public function testBuild(): void
    {
        $node = new Node(
            new NodeLabel('DemoNode'),
            [
                new Property('id', 1234),
                new Property('string', 'Hello World'),
                new Property('float', 1.23)
            ],
            new Property('id')
        );
        $statements = DeleteRelationsFromNodeStatementBuilder::build($node);

        $this->assertIsArray($statements);
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertSame(
            "MATCH\n".
            "  (child:DemoNode {id: \$id})\n".
            "  -[relation {_managedBy: \$_managedBy}]->\n".
            "  (parent)\n".
            "DELETE relation",
            $statement->getText()
        );
        $this->assertSame(
            [
                'id' => 1234,
                '_managedBy' => 'DemoNode'
            ],
            $statement->getParameters()
        );
    }

}
