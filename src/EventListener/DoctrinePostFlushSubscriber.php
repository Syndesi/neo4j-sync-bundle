<?php

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;

class DoctrinePostFlushSubscriber implements EventSubscriber
{
    private Neo4jClient $client;

    public function __construct(Neo4jClient $client)
    {
        $this->client = $client;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush,
        ];
    }

    public function postFlush()
    {
        $this->client->flush();
    }
}
