<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use Attribute;
use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Attribute\Relation;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Provider\RelationAttributeProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticIdentifierProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\Provider\StaticRelationLabelProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation as RelationVO;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class ClassWithoutRelationAttribute
{
}

#[Relation(
    new StaticRelationLabelProvider(new RelationLabel('SOME_RELATION')),
    new StaticNodeLabelProvider(new NodeLabel('ParentNode')),
    new StaticIdentifierProvider(new Property('id', 1234)),
    new StaticNodeLabelProvider(new NodeLabel('ChildNode')),
    new StaticIdentifierProvider(new Property('id', 4321)),
    new StaticPropertiesProvider([
        new Property('id', 1234),
        new Property('key', 'value'),
    ]),
    new StaticIdentifierProvider(new Property('id'))
)]
class ClassWithDefaultRelationAttribute
{
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CustomRelation implements RelationAttributeInterface
{
    public function getRelation(object $entity): RelationVO
    {
        return new RelationVO(
            new RelationLabel('SOME_RELATION'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            new NodeLabel('ChildNode'),
            new Property('id', 4321),
            [
                new Property('id', 1234),
                new Property('key', 'value'),
            ],
            new Property('id')
        );
    }
}

#[CustomRelation]
class ClassWithCustomRelationAttribute
{
}

class RelationAttributeProviderTest extends TestCase
{
    public function testGetRelationAttributesOnClassWithoutIndices(): void
    {
        $provider = new RelationAttributeProvider();
        $indices = $provider->getRelationAttribute(ClassWithoutRelationAttribute::class);
        $this->assertEmpty($indices);
        $indices = $provider->getRelationAttribute(new ClassWithoutRelationAttribute());
        $this->assertEmpty($indices);
    }

    public function testGetRelationAttributesOnClassWithDefaultIndex(): void
    {
        $provider = new RelationAttributeProvider();
        $index = $provider->getRelationAttribute(ClassWithDefaultRelationAttribute::class);
        $this->assertInstanceOf(RelationAttributeInterface::class, $index);
        $index = $provider->getRelationAttribute(new ClassWithDefaultRelationAttribute());
        $this->assertInstanceOf(RelationAttributeInterface::class, $index);
    }

    public function testGetRelationAttributesOnClassWithCustomIndex(): void
    {
        $provider = new RelationAttributeProvider();
        $index = $provider->getRelationAttribute(ClassWithCustomRelationAttribute::class);
        $this->assertInstanceOf(RelationAttributeInterface::class, $index);
        $index = $provider->getRelationAttribute(new ClassWithCustomRelationAttribute());
        $this->assertInstanceOf(RelationAttributeInterface::class, $index);
    }
}
