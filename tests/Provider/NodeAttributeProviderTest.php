<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use Attribute;
use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Provider\NodeAttributeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIdentifierProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\Node as NodeVO;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class ClassWithoutNodeAttribute
{
}

#[Node(
    new StaticNodeLabelProvider(new NodeLabel('SomeNode')),
    new StaticPropertiesProvider([
        new Property('id', 1234),
        new Property('key', 'value'),
    ]),
    new StaticIdentifierProvider(new Property('id'))
)]
class ClassWithDefaultNodeAttribute
{
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CustomNode implements NodeAttributeInterface
{
    public function getNode(object $entity): NodeVO
    {
        return new NodeVO(
            new NodeLabel('SomeNode'),
            [
                new Property('id', 1234),
                new Property('key', 'value'),
            ],
            new Property('id')
        );
    }

    public function hasRelations(): bool
    {
        return false;
    }
}

#[CustomNode]
class ClassWithCustomNodeAttribute
{
}

class NodeAttributeProviderTest extends TestCase
{
    public function testGetNodeAttributesOnClassWithoutIndices(): void
    {
        $provider = new NodeAttributeProvider();
        $indices = $provider->getNodeAttribute(ClassWithoutNodeAttribute::class);
        $this->assertEmpty($indices);
        $indices = $provider->getNodeAttribute(new ClassWithoutNodeAttribute());
        $this->assertEmpty($indices);
    }

    public function testGetNodeAttributesOnClassWithDefaultIndex(): void
    {
        $provider = new NodeAttributeProvider();
        $index = $provider->getNodeAttribute(ClassWithDefaultNodeAttribute::class);
        $this->assertInstanceOf(NodeAttributeInterface::class, $index);
        $index = $provider->getNodeAttribute(new ClassWithDefaultNodeAttribute());
        $this->assertInstanceOf(NodeAttributeInterface::class, $index);
    }

    public function testGetNodeAttributesOnClassWithCustomIndex(): void
    {
        $provider = new NodeAttributeProvider();
        $index = $provider->getNodeAttribute(ClassWithCustomNodeAttribute::class);
        $this->assertInstanceOf(NodeAttributeInterface::class, $index);
        $index = $provider->getNodeAttribute(new ClassWithCustomNodeAttribute());
        $this->assertInstanceOf(NodeAttributeInterface::class, $index);
    }
}
