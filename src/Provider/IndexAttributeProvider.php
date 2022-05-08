<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeProviderInterface;

class IndexAttributeProvider implements IndexAttributeProviderInterface
{
    public function getIndexAttribute(object $entity): ?IndexAttributeInterface
    {
        foreach ((new ReflectionClass(get_class($entity)))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof IndexAttributeInterface) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
