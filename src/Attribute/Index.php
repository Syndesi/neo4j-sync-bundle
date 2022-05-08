<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\IndexAttributeInterface;
use Syndesi\Neo4jSyncBundle\Contract\IndexNameProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\IndexTypeProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\LabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class Index implements IndexAttributeInterface
{
    public function __construct(
        private readonly IndexNameProviderInterface $indexNameProvider,
        private readonly LabelProviderInterface $labelProvider,
        private readonly PropertiesProviderInterface $propertiesProvider,
        private readonly IndexTypeProviderInterface $indexTypeProvider
    ) {
    }

    public function getIndex(object $entity): \Syndesi\Neo4jSyncBundle\ValueObject\Index
    {
        return new \Syndesi\Neo4jSyncBundle\ValueObject\Index(
            $this->indexNameProvider->getName(),
            $this->labelProvider->getLabel(null),
            $this->propertiesProvider->getProperties(null),
            $this->indexTypeProvider->getIndexType()
        );
    }
}
