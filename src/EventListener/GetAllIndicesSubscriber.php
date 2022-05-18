<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Syndesi\Neo4jSyncBundle\Event\GetAllIndicesEvent;
use Syndesi\Neo4jSyncBundle\Provider\IndexAttributeProvider;

class GetAllIndicesSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            GetAllIndicesEvent::NAME => 'onGetAllIndices',
        ];
    }

    public function onGetAllIndices(GetAllIndicesEvent $getAllIndicesEvent): void
    {
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            /** @psalm-suppress RedundantConditionGivenDocblockType */
            if ($metadata instanceof ClassMetadata) {
                $className = $metadata->getReflectionClass()->getName();
                $provider = new IndexAttributeProvider();
                foreach ($provider->getIndexAttributes($className) as $indexAttribute) {
                    $getAllIndicesEvent->addIndex($indexAttribute->getIndex());
                }
            }
        }
    }
}
