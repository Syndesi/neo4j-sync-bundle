<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use ReflectionException;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeProviderInterface;

class RelationAttributeProvider implements RelationAttributeProviderInterface
{
    /**
     * @param class-string|object $entityOrClassName
     *
     * @throws ReflectionException
     */
    public function getRelationAttribute(string|object $entityOrClassName): ?RelationAttributeInterface
    {
        $className = $entityOrClassName;
        if (is_object($entityOrClassName)) {
            $className = get_class($entityOrClassName);
        }
        /** @psalm-suppress ArgumentTypeCoercion */
        foreach ((new ReflectionClass($className))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof RelationAttributeInterface) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
