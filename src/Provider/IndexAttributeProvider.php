<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use ReflectionClass;
use Syndesi\Neo4jSyncBundle\Attribute\Index;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeProviderInterface;

class IndexAttributeProvider implements IndexAttributeProviderInterface
{
    /**
     * @return Index[]
     */
    public function getIndexAttributes(string $className): array
    {
        $attributes = [];
        foreach ((new ReflectionClass($className))->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof IndexAttributeInterface) {
                $attributes[] = $attributeInstance;
            }
        }

        return $attributes;
    }
}
