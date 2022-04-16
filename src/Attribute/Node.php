<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Attribute;

use Attribute;
use Syndesi\Neo4jSyncBundle\Contract\NodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodeRelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\DuplicatePropertiesException;
use Syndesi\Neo4jSyncBundle\Exception\MissingIdPropertyException;

#[Attribute(Attribute::TARGET_CLASS)]
class Node
{
    public function __construct(
        private readonly NodeLabelProviderInterface $nodeLabelProvider,
        private readonly NodePropertiesProviderInterface $nodePropertiesProvider,
        private readonly NodeIdentifierProviderInterface $nodeIdentifierProvider,
        private readonly ?NodeRelationsProviderInterface $nodeRelationsProvider = null
    ) {
    }

    /**
     * @throws MissingIdPropertyException
     * @throws DuplicatePropertiesException
     */
    public function getNode(object $entity): \Syndesi\Neo4jSyncBundle\ValueObject\Node
    {
        $relations = [];
        if ($this->nodeRelationsProvider) {
            $relations = $this->nodeRelationsProvider->getNodeRelations($entity);
        }

        return new \Syndesi\Neo4jSyncBundle\ValueObject\Node(
            $this->nodeLabelProvider->getNodeLabel($entity),
            $this->nodePropertiesProvider->getNodeProperties($entity),
            $this->nodeIdentifierProvider->getNodeIdentifier($entity),
            $relations
        );
    }
}
