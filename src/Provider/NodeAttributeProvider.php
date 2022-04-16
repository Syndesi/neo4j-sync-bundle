<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeProviderInterface;

class NodeAttributeProvider implements NodeAttributeProviderInterface
{
    public function getNodeAttribute(object $entity): ?Node
    {
        foreach ((new ReflectionClass(get_class($entity)))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof Node) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
