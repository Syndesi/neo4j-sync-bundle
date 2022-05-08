<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\Node;

interface NodeAttributeInterface
{

    public function getNode(object $entity): Node;

}
