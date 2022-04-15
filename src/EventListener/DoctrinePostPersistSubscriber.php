<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class DoctrinePostPersistSubscriber implements EventSubscriber
{
    public function __construct(
        private Neo4jClientInterface $client,
        private EntityReader $entityReader,
        private Neo4jStatementHelper $statementHelper
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$this->entityReader->isEntitySupported($entity)) {
            return;
        }
        $this->client->addStatements([
            ...$this->statementHelper->getNodeStatements($entity),
            ...$this->statementHelper->getRelationStatements($entity),
        ]);
    }
}
