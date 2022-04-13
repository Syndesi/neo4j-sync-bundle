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
    private Neo4jClientInterface $client;
    private EntityReader $entityReader;
    private Neo4jStatementHelper $statementHelper;

    public function __construct(
        Neo4jClientInterface $client,
        EntityReader $entityReader,
        Neo4jStatementHelper $statementHelper
    ) {
        $this->client = $client;
        $this->entityReader = $entityReader;
        $this->statementHelper = $statementHelper;
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
