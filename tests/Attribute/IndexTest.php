<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Attribute\Index;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexNameProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexTypeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

#[Index(
    new StaticIndexNameProvider(new IndexName('index')),
    new StaticNodeLabelProvider(new NodeLabel('SomeNode')),
    new StaticPropertiesProvider([
        new Property('id'),
    ]),
    new StaticIndexTypeProvider(IndexType::BTREE)
)]
class ClassWithSingleAttribute
{
}

#[Index(
    new StaticIndexNameProvider(new IndexName('index_1')),
    new StaticNodeLabelProvider(new NodeLabel('SomeNode')),
    new StaticPropertiesProvider([
        new Property('id'),
    ]),
    new StaticIndexTypeProvider(IndexType::BTREE)
)]
#[Index(
    new StaticIndexNameProvider(new IndexName('index_2')),
    new StaticNodeLabelProvider(new NodeLabel('SomeNode')),
    new StaticPropertiesProvider([
        new Property('id'),
    ]),
    new StaticIndexTypeProvider(IndexType::BTREE)
)]
class ClassWithMultipleAttributes
{
}

class IndexTest extends TestCase
{
    public function testSingleAttribute(): void
    {
        $attributes = (new ReflectionClass(ClassWithSingleAttribute::class))->getAttributes();
        $this->assertCount(1, $attributes);
        $attribute = $attributes[0]->newInstance();
        $this->assertInstanceOf(Index::class, $attribute);
        $index = $attribute->getIndex();
        $this->assertInstanceOf(\Syndesi\Neo4jSyncBundle\ValueObject\Index::class, $index);
        $this->assertSame('index', (string) $index->getName());
        $this->assertSame('SomeNode', (string) $index->getLabel());
        $this->assertCount(1, $index->getProperties());
        $this->assertSame('id', $index->getProperties()[0]->getName());
        $this->assertSame(IndexType::BTREE, $index->getType());
    }

    public function testMultipleAttributes(): void
    {
        $attributes = (new ReflectionClass(ClassWithMultipleAttributes::class))->getAttributes();
        $this->assertCount(2, $attributes);

        $attribute1 = $attributes[0]->newInstance();
        $this->assertInstanceOf(Index::class, $attribute1);
        $index1 = $attribute1->getIndex();
        $this->assertInstanceOf(\Syndesi\Neo4jSyncBundle\ValueObject\Index::class, $index1);

        $attribute2 = $attributes[1]->newInstance();
        $this->assertInstanceOf(Index::class, $attribute2);
        $index2 = $attribute2->getIndex();
        $this->assertInstanceOf(\Syndesi\Neo4jSyncBundle\ValueObject\Index::class, $index2);

        $this->assertFalse($index1->isEqualTo($index2));
    }
}
