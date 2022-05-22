<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Provider\StaticIndexTypeProvider;

class StaticIndexTypeProviderTest extends TestCase
{
    public function testStaticIndexTypeProvider(): void
    {
        $provider = new StaticIndexTypeProvider(IndexType::BTREE);
        $this->assertInstanceOf(IndexType::class, $provider->getIndexType());
        $this->assertTrue(IndexType::BTREE === $provider->getIndexType());
    }
}
