<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

interface LabelInterface
{
    public function getLabel(): string;
}
