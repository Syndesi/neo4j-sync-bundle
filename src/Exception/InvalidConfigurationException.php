<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Exception;

use Psalm\Immutable;

#[Immutable]
final class InvalidConfigurationException extends Neo4jSyncException
{
}
