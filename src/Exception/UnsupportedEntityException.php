<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Exception;

use Psalm\Immutable;

#[Immutable]
final class UnsupportedEntityException extends Neo4jSyncException
{
}
