<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

interface RelationLabelProviderInterface extends LabelProviderInterface
{
    public function getLabel(object $entity): RelationLabel;
}
