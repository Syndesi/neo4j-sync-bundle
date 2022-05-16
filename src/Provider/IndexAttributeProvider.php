<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeProviderInterface;

class IndexAttributeProvider implements IndexAttributeProviderInterface
{
    /**
     * @return list<IndexAttributeInterface>
     *
     * @throws ReflectionException
     */
    public function getIndexAttributes(string|object $entityOrClassName): array
    {
        $className = $entityOrClassName;
        if (is_object($entityOrClassName)) {
            $className = get_class($entityOrClassName);
        }
        $attributes = [];
        /* @psalm-suppress ArgumentTypeCoercion */
        foreach ((new ReflectionClass($className))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof IndexAttributeInterface) {
                $attributes[] = $attributeInstance;
            }
        }

        return $attributes;
    }
}
