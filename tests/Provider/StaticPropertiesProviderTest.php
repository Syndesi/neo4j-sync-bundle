<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticPropertiesProviderTest extends TestCase
{
    public function testStaticPropertiesProvider(): void
    {
        $provider = new StaticPropertiesProvider([
            new Property('id', 1234),
            new Property('key', 'value'),
        ]);
        $this->assertCount(2, $provider->getProperties((object) []));
        $this->assertInstanceOf(Property::class, $provider->getProperties((object) [])[0]);
        $this->assertTrue($provider->getProperties((object) [])[0]->isEqualTo(new Property('id', 1234)));
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticPropertiesProvider([
            (object) [],
        ]);
    }
}
