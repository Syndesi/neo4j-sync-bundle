<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Contract\LabelInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class CustomLabel implements LabelInterface
{
    public function __construct()
    {
    }

    public function isEqualTo(object $element): bool
    {
        return true;
    }

    public function getLabel(): string
    {
        return '';
    }

    public function __toString()
    {
        return '';
    }
}

class IndexTest extends TestCase
{
    public function testValidNodeIndex(): void
    {
        $index = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $this->assertSame('demo_index', (string) $index->getName());
        $this->assertSame('DemoNode', (string) $index->getLabel());
        $this->assertCount(1, $index->getProperties());
        $this->assertSame('id', $index->getProperties()[0]->getName());
        $this->assertSame(IndexType::BTREE, $index->getType());
    }

    public function testValidRelationIndex(): void
    {
        $index = new Index(
            new IndexName('demo_index'),
            new RelationLabel('DEMO_RELATION'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $this->assertSame('demo_index', (string) $index->getName());
        $this->assertSame('DEMO_RELATION', (string) $index->getLabel());
        $this->assertCount(1, $index->getProperties());
        $this->assertSame('id', $index->getProperties()[0]->getName());
        $this->assertSame(IndexType::BTREE, $index->getType());
    }

    public function testEmptyProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [],
            IndexType::BTREE
        );
    }

    public function testInvalidLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Index(
            new IndexName('demo_index'),
            new CustomLabel(),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
    }

    public function testStringableNode(): void
    {
        $index = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $this->assertSame('BTREE INDEX demo_index FOR (node:DemoNode) ON (node.id)', (string) $index);
    }

    public function testStringableRelation(): void
    {
        $index = new Index(
            new IndexName('demo_index'),
            new RelationLabel('DEMO_RELATION'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $this->assertSame('BTREE INDEX demo_index FOR ()-[relation:DEMO_RELATION]-() ON (relation.id)', (string) $index);
    }

    public function testEqual(): void
    {
        $nodeIndex1 = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $nodeIndex2 = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $nodeIndex3 = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('otherId'),
            ],
            IndexType::BTREE
        );
        $nodeIndex4 = new Index(
            new IndexName('demo_index'),
            new NodeLabel('DemoNode'),
            [
                new Property('id'),
                new Property('otherProperty'),
            ],
            IndexType::BTREE
        );
        $relationIndex = new Index(
            new IndexName('demo_index'),
            new RelationLabel('DEMO_RELATION'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
        $this->assertTrue($nodeIndex1->isEqualTo($nodeIndex1));
        $this->assertTrue($nodeIndex1->isEqualTo($nodeIndex2));
        $this->assertTrue($nodeIndex2->isEqualTo($nodeIndex1));
        $this->assertFalse($nodeIndex1->isEqualTo((object) []));
        $this->assertFalse($nodeIndex1->isEqualTo($relationIndex));
        $this->assertFalse($nodeIndex1->isEqualTo($nodeIndex3));
        $this->assertFalse($nodeIndex1->isEqualTo($nodeIndex4));
    }
}
