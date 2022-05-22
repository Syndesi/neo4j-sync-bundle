<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class StaticNodeLabelProviderTest extends TestCase
{
    public function testStaticNodeLabelProvider(): void
    {
        $provider = new StaticNodeLabelProvider(new NodeLabel('SomeNode'));
        $this->assertInstanceOf(NodeLabel::class, $provider->getLabel());
        $this->assertTrue($provider->getLabel()->isEqualTo(new NodeLabel('SomeNode')));
    }
}
