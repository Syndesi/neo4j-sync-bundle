<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class StaticNodePropertiesProvider implements NodePropertiesProviderInterface
{
    /**
     * @throws DuplicatePropertiesException
     */
    public function __construct(
        /**
         * @var Property[] $nodeProperties
         */
        private readonly array $nodeProperties
    ) {
    }

    public function getNodeProperties(object $entity): array
    {
        return $this->nodeProperties;
    }
}
