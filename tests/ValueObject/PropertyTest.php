<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class PropertyTest extends TestCase {

    public function testValidProperty(): void
    {
        $property = new Property('someName', 'someValue');
        $this->assertSame('someName', $property->getName());
        $this->assertSame('someValue', $property->getValue());
    }

    public function testInvalidProperty(): void
    {
        $this->expectException(UnsupportedPropertyNameException::class);
        new Property('SomeName', 'someValue');
    }

    public function testStringable(): void
    {
        $property = new Property('someName', 'someValue');
        $this->assertSame('someName: someValue', (string) $property);
    }

}
