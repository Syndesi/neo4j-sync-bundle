<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\MissingPropertyException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Provider\RelationAttributeProvider;
use Syndesi\Neo4jSyncBundle\Statement\MergeRelationStatementBuilder;

class DoctrinePostUpdateRelationSubscriber implements EventSubscriber
{
    private Neo4jClientInterface $client;
    private RelationAttributeProviderInterface $relationAttributeProvider;

    public function __construct(
        Neo4jClientInterface $client
    ) {
        $this->client = $client;
        $this->relationAttributeProvider = new RelationAttributeProvider();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    /**
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $relationAttribute = $this->relationAttributeProvider->getRelationAttribute($entity);
        if (!$relationAttribute) {
            return;
        }

        $relation = $relationAttribute->getRelation($entity);
        $this->client->addStatements([
            ...MergeRelationStatementBuilder::build($relation),
        ]);
    }
}
