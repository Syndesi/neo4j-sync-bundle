<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface LabelProviderInterface
{
    public function getLabel(object $entity): LabelInterface;
}
