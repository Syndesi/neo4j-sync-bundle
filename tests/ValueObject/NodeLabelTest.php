<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedNodeLabelException;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class NodeLabelTest extends TestCase
{
    public function testValidNodeLabel(): void
    {
        $property = new NodeLabel('SomeNode');
        $this->assertSame('SomeNode', $property->getLabel());
    }

    public function testInvalidNodeLabel(): void
    {
        $this->expectException(UnsupportedNodeLabelException::class);
        new NodeLabel('SOME_NODE');
    }

    public function testStringable(): void
    {
        $property = new NodeLabel('SomeNode');
        $this->assertSame('SomeNode', (string) $property);
    }

    public function testEqual(): void
    {
        $property = new NodeLabel('SomeNode');
        $this->assertTrue($property->isEqualTo(new NodeLabel('SomeNode')));
        $this->assertFalse($property->isEqualTo(new NodeLabel('ChangedNode')));
        $this->assertFalse($property->isEqualTo((object) []));
    }
}
