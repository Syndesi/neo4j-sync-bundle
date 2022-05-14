<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Syndesi\Neo4jSyncBundle\Event\DatabaseSyncEvent;
use Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncRelationProvider;
use Syndesi\Neo4jSyncBundle\Provider\RelationAttributeProvider;

class DatabaseSyncRelationSubscriber implements EventSubscriberInterface
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
                100,
            ],
        ];
    }

    public function onDatabaseSync(DatabaseSyncEvent $databaseSyncEvent)
    {
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata instanceof ClassMetadata) {
                $className = $metadata->getReflectionClass()->getName();
                $relationAttribute = (new RelationAttributeProvider())->getRelationAttribute($className);
                if ($relationAttribute) {
                    $tmpProvider = new DatabaseSyncRelationProvider($className, $this->em, $relationAttribute);
                    $databaseSyncEvent->addPaginatedStatementProvider($tmpProvider);
                }
            }
        }
    }
}
