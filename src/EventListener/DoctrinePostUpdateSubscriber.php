<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class DoctrinePostUpdateSubscriber implements EventSubscriber
{
    private Neo4jClientInterface $client;

    public function __construct(
        Neo4jClientInterface $client
    ) {
        $this->client = $client;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
//        if (!$this->entityReader->isEntitySupported($entity)) {
//            return;
//        }
//        $this->client->addStatements([
//            ...$this->statementHelper->getNodeStatements($entity, CreateType::MERGE),
//            ...$this->statementHelper->getRelationStatements($entity, CreateType::MERGE),
//        ]);
    }
}
