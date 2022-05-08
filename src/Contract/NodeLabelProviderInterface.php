<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

interface NodeLabelProviderInterface extends LabelProviderInterface
{
    public function getLabel(object $entity): NodeLabel;
}
