<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeProviderInterface;
use Syndesi\Neo4jSyncBundle\Provider\NodeAttributeProvider;
use Syndesi\Neo4jSyncBundle\Statement\CreateOrUpdateNodeWithRelationsStatementBuilder;

class DoctrinePostPersistNodeSubscriber implements EventSubscriber
{
    private NodeAttributeProviderInterface $nodeAttributeProvider;

    public function __construct(
        private Neo4jClientInterface $client,
        private bool $disableSubscriber = false
    ) {
        $this->nodeAttributeProvider = new NodeAttributeProvider();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        if ($this->disableSubscriber) {
            return;
        }
        $entity = $args->getEntity();
        $nodeAttribute = $this->nodeAttributeProvider->getNodeAttribute($entity);
        if (!$nodeAttribute) {
            return;
        }

        $node = $nodeAttribute->getNode($entity);
        $this->client->addStatements([
            ...CreateOrUpdateNodeWithRelationsStatementBuilder::build($node),
        ]);
    }
}
