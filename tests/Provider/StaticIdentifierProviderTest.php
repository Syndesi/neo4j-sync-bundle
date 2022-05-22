<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Provider\StaticIdentifierProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticIdentifierProviderTest extends TestCase
{
    public function testStaticIdentifierProvider(): void
    {
        $provider = new StaticIdentifierProvider(new Property('key', 'value'));
        $this->assertInstanceOf(Property::class, $provider->getIdentifier((object) []));
        $this->assertTrue($provider->getIdentifier((object) [])->isEqualTo(new Property('key', 'value')));
    }
}
