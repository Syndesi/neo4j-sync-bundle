<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedIndexNameException;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;

class IndexNameTest extends TestCase
{
    public function testValidIndexName(): void
    {
        $property = new IndexName('some_index');
        $this->assertSame('some_index', $property->getName());
    }

    public function testInvalidIndexName(): void
    {
        $this->expectException(UnsupportedIndexNameException::class);
        new IndexName('SomeIndex');
    }

    public function testStringable(): void
    {
        $property = new IndexName('some_index');
        $this->assertSame('some_index', (string) $property);
    }

    public function testEqual(): void
    {
        $property = new IndexName('some_index');
        $this->assertTrue($property->isEqualTo(new IndexName('some_index')));
        $this->assertFalse($property->isEqualTo(new IndexName('other_index')));
        $this->assertFalse($property->isEqualTo((object) []));
    }
}
