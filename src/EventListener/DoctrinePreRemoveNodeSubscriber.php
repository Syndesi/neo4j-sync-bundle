<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Provider\NodeAttributeProvider;
use Syndesi\Neo4jSyncBundle\Statement\DeleteNodeStatementBuilder;

class DoctrinePreRemoveNodeSubscriber implements EventSubscriber
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
            Events::preRemove,
        ];
    }

    /**
     * @throws MissingIdPropertyException
     * @throws MissingPropertyException
     * @throws DuplicatePropertiesException
     */
    public function preRemove(LifecycleEventArgs $args)
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
            ...DeleteNodeStatementBuilder::build($node),
        ]);
    }
}
