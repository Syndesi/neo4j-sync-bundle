<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Event\DatabaseSyncEvent;
use Syndesi\Neo4jSyncBundle\EventListener\DatabaseSyncNodeRelationSubscriber;
use Syndesi\Neo4jSyncBundle\EventListener\DatabaseSyncNodeSubscriber;
use Syndesi\Neo4jSyncBundle\EventListener\DatabaseSyncRelationSubscriber;

class DatabaseSyncPriorityTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testPriority(): void
    {
        // sync relations < sync node relations < sync nodes
        $this->assertGreaterThan(
            DatabaseSyncRelationSubscriber::getSubscribedEvents()[DatabaseSyncEvent::NAME][1],
            DatabaseSyncNodeRelationSubscriber::getSubscribedEvents()[DatabaseSyncEvent::NAME][1],
        );
        $this->assertGreaterThan(
            DatabaseSyncNodeRelationSubscriber::getSubscribedEvents()[DatabaseSyncEvent::NAME][1],
            DatabaseSyncNodeSubscriber::getSubscribedEvents()[DatabaseSyncEvent::NAME][1],
        );
    }
}
