<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class DoctrinePostFlushSubscriber implements EventSubscriber
{
    private Neo4jClientInterface $client;

    public function __construct(Neo4jClientInterface $client)
    {
        $this->client = $client;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush,
        ];
    }

    public function postFlush(): void
    {
        $this->client->flush();
    }
}
