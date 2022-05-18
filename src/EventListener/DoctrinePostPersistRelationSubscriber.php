<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeProviderInterface;
use Syndesi\Neo4jSyncBundle\Provider\RelationAttributeProvider;
use Syndesi\Neo4jSyncBundle\Statement\MergeRelationStatementBuilder;

class DoctrinePostPersistRelationSubscriber implements EventSubscriber
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
