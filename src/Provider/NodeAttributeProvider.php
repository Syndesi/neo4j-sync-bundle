<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeAttributeProviderInterface;

class NodeAttributeProvider implements NodeAttributeProviderInterface
{
    /**
     * @param class-string|object $entityOrClassName
     *
     * @throws \ReflectionException
     */
    public function getNodeAttribute(string|object $entityOrClassName): ?NodeAttributeInterface
    {
        $className = $entityOrClassName;
        if (is_object($entityOrClassName)) {
            $className = get_class($entityOrClassName);
        }
        /** @psalm-suppress ArgumentTypeCoercion */
        foreach ((new ReflectionClass($className))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof NodeAttributeInterface) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
