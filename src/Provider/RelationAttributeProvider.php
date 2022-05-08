<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\RelationAttributeProviderInterface;

class RelationAttributeProvider implements RelationAttributeProviderInterface
{
    public function getRelationAttribute(object $entity): ?RelationAttributeInterface
    {
        foreach ((new ReflectionClass(get_class($entity)))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof RelationAttributeInterface) {
                return $attributeInstance;
            }
        }

        return null;
    }
}
