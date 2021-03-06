<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\ValueObject\Node;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class NodeTest extends TestCase
{
    public function testValidNode(): void
    {
        $nodeLabel = new NodeLabel('NodeLabel');
        $properties = [
            new Property('id', 1234),
            new Property('someProperty', 'someValue'),
        ];
        $identifier = new Property('id');
        $relations = [
            new Relation(
                new RelationLabel('SOME_RELATION'),
                new NodeLabel('OtherNode'),
                new Property('id', 'someValue'),
                $nodeLabel,
                new Property('id', 1234)
            ),
        ];
        $node = new Node($nodeLabel, $properties, $identifier, $relations);

        $this->assertSame($nodeLabel, $node->getLabel());
        $this->assertSame($properties, $node->getProperties());
        $this->assertSame($identifier->getName(), $node->getIdentifier()->getName());
        $this->assertSame(1234, $node->getIdentifier()->getValue());
        $this->assertSame($relations, $node->getRelations());
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Node(
            new NodeLabel('NodeLabel'),
            [
                new DateTime(),
            ],
            new Property('id')
        );
    }

    public function testDuplicatePropertiesException(): void
    {
        $this->expectException(DuplicatePropertiesException::class);
        new Node(
            new NodeLabel('NodeLabel'),
            [
                new Property('id', 1234),
                new Property('someProperty', 'someValue'),
                new Property('someProperty', 'otherValue'),
            ],
            new Property('id')
        );
    }

    public function testMissingIdPropertyException(): void
    {
        $this->expectException(MissingIdPropertyException::class);
        new Node(
            new NodeLabel('NodeLabel'),
            [
                new Property('someProperty', 'someValue'),
            ],
            new Property('id')
        );
    }

    public function testGetPropertiesAsAssociativeArray(): void
    {
        $node = new Node(
            new NodeLabel('NodeLabel'),
            [
                new Property('id', 1234),
                new Property('someProperty', 'someValue'),
            ],
            new Property('id')
        );
        $this->assertSame(
            [
                'id' => 1234,
                'someProperty' => 'someValue',
            ],
            $node->getPropertiesAsAssociativeArray()
        );
    }

    public function testGetProperty(): void
    {
        $idProperty = new Property('id', 1234);
        $stringProperty = new Property('someProperty', 'someValue');
        $node = new Node(
            new NodeLabel('NodeLabel'),
            [
                $idProperty,
                $stringProperty,
            ],
            new Property('id')
        );

        $this->assertSame($idProperty->getValue(), $node->getProperty($idProperty->getName()));
        $this->assertSame($stringProperty->getValue(), $node->getProperty($stringProperty->getName()));
        $this->assertSame($idProperty->getValue(), $node->getProperty($node->getIdentifier()->getName()));

        $this->expectException(MissingPropertyException::class);
        $node->getProperty('thisPropertyDoesNotExist');
    }

    public function testAreAllRelationsIdentifiable(): void
    {
        $nodeLabel = new NodeLabel('NodeLabel');
        $properties = [
            new Property('id', 1234),
            new Property('someProperty', 'someValue'),
        ];
        $identifier = new Property('id');
        $anonymousRelation = new Relation(
            new RelationLabel('SOME_RELATION'),
            new NodeLabel('OtherNode'),
            new Property('id', 'someValue'),
            $nodeLabel,
            new Property('id', 1234)
        );
        $identifiableRelation = new Relation(
            new RelationLabel('SOME_RELATION'),
            new NodeLabel('OtherNode'),
            new Property('id', 'someValue'),
            $nodeLabel,
            new Property('id', 1234),
            [
                new Property('relationId', 1234),
            ],
            new Property('relationId')
        );

        $anonymousNode = new Node($nodeLabel, $properties, $identifier, [$anonymousRelation]);
        $this->assertFalse($anonymousNode->areAllRelationsIdentifiable());

        $identifiableNode = new Node($nodeLabel, $properties, $identifier, [$identifiableRelation]);
        $this->assertTrue($identifiableNode->areAllRelationsIdentifiable());

        $mixedNode = new Node($nodeLabel, $properties, $identifier, [$anonymousRelation, $identifiableRelation]);
        $this->assertFalse($mixedNode->areAllRelationsIdentifiable());

        $emptyNode = new Node($nodeLabel, $properties, $identifier);
        $this->assertFalse($emptyNode->areAllRelationsIdentifiable());
    }

    public function testStringable(): void
    {
        $node = new Node(
            new NodeLabel('NodeLabel'),
            [
                new Property('id', 1234),
                new Property('someProperty', 'someValue'),
            ],
            new Property('id')
        );
        $this->assertSame('NodeLabel {id: 1234, someProperty: someValue}', (string) $node);
    }
}
