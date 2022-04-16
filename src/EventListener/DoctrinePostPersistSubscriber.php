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
use Syndesi\Neo4jSyncBundle\Provider\NodeAttributeProvider;
use Syndesi\Neo4jSyncBundle\Statement\MergeNodeStatementBuilder;

class DoctrinePostPersistSubscriber implements EventSubscriber
{
    private Neo4jClientInterface $client;
    private NodeAttributeProviderInterface $nodeAttributeProvider;
    private MergeNodeStatementBuilder $mergeNodeStatementBuilder;

    public function __construct(
        Neo4jClientInterface $client
    ) {
        $this->client = $client;
        $this->nodeAttributeProvider = new NodeAttributeProvider();
        $this->mergeNodeStatementBuilder = new MergeNodeStatementBuilder();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * @throws MissingIdPropertyException
     * @throws DuplicatePropertiesException
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $nodeAttribute = $this->nodeAttributeProvider->getNodeAttribute($entity);
        if (!$nodeAttribute) {
            return;
        }

        $node = $nodeAttribute->getNode($entity);
        $this->client->addStatements([
            ...$this->mergeNodeStatementBuilder->getStatements($node),
        ]);
    }
}
