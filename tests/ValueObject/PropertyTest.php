<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class PropertyTest extends TestCase
{
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

    public function testEqual(): void
    {
        $property = new Property('someName', 'someValue');
        $this->assertTrue($property->isEqualTo(new Property('someName', 'someValue')));
        $this->assertFalse($property->isEqualTo(new Property('someName', 'changedValue')));
        $this->assertFalse($property->isEqualTo(new Property('changedName', 'someValue')));
        $this->assertFalse($property->isEqualTo((object) []));
    }
}
