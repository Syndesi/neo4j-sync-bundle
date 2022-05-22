<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Provider\StaticRelationLabelProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class StaticRelationLabelProviderTest extends TestCase
{
    public function testStaticRelationLabelProvider(): void
    {
        $provider = new StaticRelationLabelProvider(new RelationLabel('SOME_RELATION'));
        $this->assertInstanceOf(RelationLabel::class, $provider->getLabel());
        $this->assertTrue($provider->getLabel()->isEqualTo(new RelationLabel('SOME_RELATION')));
    }
}
