<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Stringable;

interface LabelInterface extends IsEqualToInterface, Stringable
{
    public function getLabel(): string;
}
