<?php

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementService;
use Syndesi\Neo4jSyncBundle\Service\StatementStore;

class Neo4jDoctrineSubscriber implements EventSubscriber
{
    private StatementStore $statementStore;
    private Neo4jStatementService $neo4jStatementService;

    public function __construct(StatementStore $statementStore, Neo4jStatementService $neo4jStatementService)
    {
        $this->statementStore = $statementStore;
        $this->neo4jStatementService = $neo4jStatementService;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
//            Events::preUpdate,
            Events::postUpdate,
//            Events::preRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        try {
            $createStatements = $this->neo4jStatementService->getCreateStatement($entity);
            $this->statementStore->addNodeStatements($createStatements);
        } catch (UnsupportedEntityException $exception) {
        }
    }

//    public function preUpdate(PreUpdateEventArgs $args){
//        $entity = $args->getEntity();
//        if (!$this->neo4jManager->isNeo4jEntity($entity)) {
//            return;
//        }
//        $this->neo4jManager->preUpdate($args);
//    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        try {
            $updateStatements = $this->neo4jStatementService->getUpdateStatement($entity);
            $this->statementStore->addNodeStatements($updateStatements);
        } catch (UnsupportedEntityException $exception) {
        }
    }

//    public function preRemove(LifecycleEventArgs $args){
//        $entity = $args->getEntity();
//        if (!$this->neo4jManager->isNeo4jEntity($entity)) {
//            return;
//        }
//        $this->neo4jManager->delete($entity);
//    }
}
