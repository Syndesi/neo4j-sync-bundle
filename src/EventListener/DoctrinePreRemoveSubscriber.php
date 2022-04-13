<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class DoctrinePreRemoveSubscriber implements EventSubscriber
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
            Events::preRemove,
        ];
    }

    /**
     * @throws ReflectionException
     * @throws MissingIdPropertyException
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$this->entityReader->isEntitySupported($entity)) {
            return;
        }
        $this->client->addStatements(
            $this->statementHelper->getDeleteStatements($entity)
        );
    }
}
