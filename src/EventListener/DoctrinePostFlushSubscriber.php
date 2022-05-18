<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class DoctrinePostFlushSubscriber implements EventSubscriber
{
    public function __construct(
        private Neo4jClientInterface $client,
        private bool $disableSubscriber = false
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush,
        ];
    }

    public function postFlush(): void
    {
        if ($this->disableSubscriber) {
            return;
        }
        $this->client->flush();
    }
}
