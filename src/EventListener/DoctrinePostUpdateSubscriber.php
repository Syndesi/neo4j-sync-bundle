<?php

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\CreateType;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class DoctrinePostUpdateSubscriber implements EventSubscriber
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
            Events::postUpdate,
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->entityReader->isEntitySupported($entity)) {
            return;
        }
        $this->client->addStatements([
            ...$this->statementHelper->getNodeStatements($entity, CreateType::MERGE),
            ...$this->statementHelper->getRelationStatements($entity, CreateType::MERGE),
        ]);
    }
}
