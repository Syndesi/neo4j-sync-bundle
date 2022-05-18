<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Statement;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Statement\MergeNodeStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class MergeNodeStatementBuilderTest extends TestCase {

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
        $statements = MergeNodeStatementBuilder::build($node);

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
                'float' => 1.23
            ],
            $statement->getParameters()
        );
    }

}
