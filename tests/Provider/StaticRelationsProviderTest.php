<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Provider\StaticRelationsProvider;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\Relation;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class StaticRelationsProviderTest extends TestCase
{
    public function testStaticRelationsProvider(): void
    {
        $provider = new StaticRelationsProvider([
            new Relation(
                new RelationLabel('SOME_RELATION'),
                new NodeLabel('ParentNode'),
                new Property('id', 1234),
                new NodeLabel('ChildNode'),
                new Property('id', 4321),
            ),
            new Relation(
                new RelationLabel('OTHER_RELATION'),
                new NodeLabel('ParentNode'),
                new Property('id', 1234),
                new NodeLabel('ChildNode'),
                new Property('id', 4321),
            ),
        ]);
        $this->assertCount(2, $provider->getRelations((object) []));
        $this->assertInstanceOf(Relation::class, $provider->getRelations((object) [])[0]);
        $this->assertTrue($provider->getRelations((object) [])[1]->isEqualTo(
            new Relation(
                new RelationLabel('OTHER_RELATION'),
                new NodeLabel('ParentNode'),
                new Property('id', 1234),
                new NodeLabel('ChildNode'),
                new Property('id', 4321),
            )
        ));
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticRelationsProvider([
            (object) [],
        ]);
    }
}
