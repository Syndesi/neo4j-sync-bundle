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
use Syndesi\Neo4jSyncBundle\Statement\DeleteRelationStatementBuilder;

class DoctrinePreRemoveRelationSubscriber implements EventSubscriber
{
    private RelationAttributeProviderInterface $relationAttributeProvider;

    public function __construct(
        private Neo4jClientInterface $client,
        private bool $disableSubscriber = false
    ) {
        $this->relationAttributeProvider = new RelationAttributeProvider();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    /**
     * @throws MissingPropertyException
     * @throws UnsupportedPropertyNameException
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->disableSubscriber) {
            return;
        }
        $entity = $args->getEntity();
        $relationAttribute = $this->relationAttributeProvider->getRelationAttribute($entity);
        if (!$relationAttribute) {
            return;
        }

        $relation = $relationAttribute->getRelation($entity);
        $this->client->addStatements([
            ...DeleteRelationStatementBuilder::build($relation),
        ]);
    }
}
