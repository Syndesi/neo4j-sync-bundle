<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class PropertyTest extends TestCase {

    public function testValidProperty(){
        $property = new Property('someName', 'someValue');
        $this->assertSame('someName', $property->getName());
        $this->assertSame('someValue', $property->getValue());
    }

    public function testInvalidProperty(){
        $this->expectException(UnsupportedPropertyNameException::class);
        new Property('SomeName', 'someValue');
    }

    public function testStringable(){
        $property = new Property('someName', 'someValue');
        $this->assertSame('someName: someValue', (string) $property);
    }

}
