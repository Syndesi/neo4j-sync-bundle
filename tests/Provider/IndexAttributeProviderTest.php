<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use Attribute;
use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Attribute\Index;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Provider\IndexAttributeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexNameProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexTypeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\Index as IndexVO;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class ClassWithoutIndexAttribute
{
}

#[Index(
    new StaticIndexNameProvider(new IndexName('some_index')),
    new StaticNodeLabelProvider(new NodeLabel('SomeNode')),
    new StaticPropertiesProvider([
        new Property('id'),
    ]),
    new StaticIndexTypeProvider(IndexType::BTREE)
)]
class ClassWithDefaultIndexAttribute
{
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CustomIndex implements IndexAttributeInterface
{
    public function getIndex(): IndexVO
    {
        return new IndexVO(
            new IndexName('some_index'),
            new NodeLabel('SomeNode'),
            [
                new Property('id'),
            ],
            IndexType::BTREE
        );
    }
}

#[CustomIndex]
class ClassWithCustomIndexAttribute
{
}

class IndexAttributeProviderTest extends TestCase
{
    public function testGetIndexAttributesOnClassWithoutIndices(): void
    {
        $provider = new IndexAttributeProvider();
        $indices = $provider->getIndexAttributes(ClassWithoutIndexAttribute::class);
        $this->assertEmpty($indices);
        $indices = $provider->getIndexAttributes(new ClassWithoutIndexAttribute());
        $this->assertEmpty($indices);
    }

    public function testGetIndexAttributesOnClassWithDefaultIndex(): void
    {
        $provider = new IndexAttributeProvider();
        $indices = $provider->getIndexAttributes(ClassWithDefaultIndexAttribute::class);
        $this->assertCount(1, $indices);
        $this->assertInstanceOf(IndexAttributeInterface::class, $indices[0]);
        $indices = $provider->getIndexAttributes(new ClassWithDefaultIndexAttribute());
        $this->assertCount(1, $indices);
        $this->assertInstanceOf(IndexAttributeInterface::class, $indices[0]);
    }

    public function testGetIndexAttributesOnClassWithCustomIndex(): void
    {
        $provider = new IndexAttributeProvider();
        $indices = $provider->getIndexAttributes(ClassWithCustomIndexAttribute::class);
        $this->assertCount(1, $indices);
        $this->assertInstanceOf(IndexAttributeInterface::class, $indices[0]);
        $indices = $provider->getIndexAttributes(new ClassWithCustomIndexAttribute());
        $this->assertCount(1, $indices);
        $this->assertInstanceOf(IndexAttributeInterface::class, $indices[0]);
    }
}
