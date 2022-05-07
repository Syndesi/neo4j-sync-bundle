<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyValueException;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class RelationTest extends TestCase {


    public function testValidRelation(){
        $relationLabel = new RelationLabel('RELATION_LABEL');
        $parentNodeLabel = new NodeLabel('ParentNode');
        $parentNodeIdentifier = new Property('id', 4321);
        $childNodeLabel = new NodeLabel('ChildNode');
        $childNodeIdentifier = new Property('id', 1234);
        $properties = [
            new Property('id', 1234),
            new Property('someProperty', 'someValue'),
        ];
        $identifier = new Property('id');

        $relation = new Relation(
            $relationLabel,
            $parentNodeLabel,
            $parentNodeIdentifier,
            $childNodeLabel,
            $childNodeIdentifier,
            $properties,
            $identifier
        );

        $this->assertSame($relationLabel, $relation->getLabel());
        $this->assertSame($childNodeLabel, $relation->getRelatesFromLabel());
        $this->assertSame($childNodeIdentifier, $relation->getRelatesFromIdentifier());
        $this->assertSame($parentNodeLabel, $relation->getRelatesToLabel());
        $this->assertSame($parentNodeIdentifier, $relation->getRelatesToIdentifier());
        $this->assertSame($properties, $relation->getProperties());
        $this->assertSame($identifier, $relation->getIdentifier());
    }

    public function testParentMissingPropertyValueException(){
        $this->expectException(MissingPropertyValueException::class);
        new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id')
        );
    }

    public function testChildMissingPropertyValueException(){
        $this->expectException(MissingPropertyValueException::class);
        new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            new NodeLabel('ChildNode'),
            new Property('id')
        );
    }

    public function testInvalidArgumentException(){
        $this->expectException(InvalidArgumentException::class);
        new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            properties: [
                new DateTime()
            ]
        );
    }

    public function testDuplicatePropertiesException(){
        $this->expectException(DuplicatePropertiesException::class);
        new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            properties: [
                new Property('someProperty', 'someValue'),
                new Property('someProperty', 'otherValue')
            ]
        );
    }

    public function testMissingIdPropertyException(){
        $this->expectException(MissingIdPropertyException::class);
        new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            properties: [
                new Property('someProperty', 'someValue')
            ],
            identifier: new Property('id')
        );
    }

    public function testGetPropertiesAsAssociativeArray(){
        $relation = new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            properties: [
                new Property('someProperty', 'someValue'),
                new Property('otherProperty', 'otherValue'),
            ]
        );
        $this->assertSame(
            [
                'someProperty' => 'someValue',
                'otherProperty' => 'otherValue'
            ],
            $relation->getPropertiesAsAssociativeArray()
        );
    }

    public function testGetProperty(){
        $idProperty = new Property('id', 1234);
        $stringProperty = new Property('someProperty', 'someValue');
        $relation = new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 1234),
            properties: [
                $idProperty,
                $stringProperty
            ],
            identifier: new Property('id')
        );

        $this->assertSame($idProperty->getValue(), $relation->getProperty($idProperty->getName()));
        $this->assertSame($stringProperty->getValue(), $relation->getProperty($stringProperty->getName()));
        $this->assertSame($idProperty->getValue(), $relation->getProperty($relation->getIdentifier()->getName()));

        $this->expectException(MissingPropertyException::class);
        $relation->getProperty('thisPropertyDoesNotExist');
    }

    public function testStringable(){
        $relation = new Relation(
            new RelationLabel('RELATION_LABEL'),
            new NodeLabel('ParentNode'),
            new Property('id', 4321),
            new NodeLabel('ChildNode'),
            new Property('id', 1234),
            [
                new Property('id', 1234),
                new Property('someProperty', 'someValue'),
            ],
            new Property('id')
        );
        $this->assertSame('(:ChildNode {id: 1234})-[:RELATION_LABEL {id: 1234, someProperty: someValue}]->(:ParentNode {id: 4321})', (string) $relation);
    }

}
