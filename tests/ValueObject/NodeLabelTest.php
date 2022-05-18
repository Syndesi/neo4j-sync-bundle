<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedRelationLabelException;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class NodeLabelTest extends TestCase {

    public function testValidRelationLabel(): void
    {
        $property = new RelationLabel('SOME_NAME');
        $this->assertSame('SOME_NAME', $property->getLabel());
    }

    public function testInvalidRelationLabel(): void
    {
        $this->expectException(UnsupportedRelationLabelException::class);
        new RelationLabel('someName');
    }

    public function testStringable(): void
    {
        $property = new RelationLabel('SOME_NAME');
        $this->assertSame('SOME_NAME', (string) $property);
    }

}
