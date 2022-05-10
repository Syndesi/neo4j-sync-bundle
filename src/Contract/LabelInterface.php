<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface LabelInterface extends IsEqualToInterface
{
    public function getLabel(): string;
}
