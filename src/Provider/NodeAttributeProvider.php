<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeProviderInterface;

class NodeAttributeProvider implements NodeAttributeProviderInterface
{
    public function getNodeAttribute(object $entity): ?NodeAttributeInterface
    {
        foreach ((new ReflectionClass(get_class($entity)))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof NodeAttributeInterface) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
