<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Syndesi\Neo4jSyncBundle\Event\DatabaseSyncEvent;
use Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeRelationProvider;
use Syndesi\Neo4jSyncBundle\Provider\NodeAttributeProvider;

class DatabaseSyncNodeRelationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            DatabaseSyncEvent::NAME => [
                'onDatabaseSync',
                200,
            ],
        ];
    }

    public function onDatabaseSync(DatabaseSyncEvent $databaseSyncEvent)
    {
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata instanceof ClassMetadata) {
                $className = $metadata->getReflectionClass()->getName();
                $nodeAttribute = (new NodeAttributeProvider())->getNodeAttribute($className);
                if ($nodeAttribute) {
                    if ($nodeAttribute->hasRelations()) {
                        $tmpProvider = new DatabaseSyncNodeRelationProvider($className, $this->em, $nodeAttribute);
                        $databaseSyncEvent->addPaginatedStatementProvider($tmpProvider);
                    }
                }
            }
        }
    }
}
