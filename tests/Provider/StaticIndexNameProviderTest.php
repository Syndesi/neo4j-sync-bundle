<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexNameProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;

class StaticIndexNameProviderTest extends TestCase
{
    public function testStaticIndexNameProvider(): void
    {
        $provider = new StaticIndexNameProvider(new IndexName('some_index'));
        $this->assertInstanceOf(IndexName::class, $provider->getName());
        $this->assertTrue($provider->getName()->isEqualTo(new IndexName('some_index')));
    }
}
