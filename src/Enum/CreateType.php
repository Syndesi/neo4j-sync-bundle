<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Enum;

enum CreateType: string
{
    case CREATE = 'CREATE';
    case MERGE = 'MERGE';
}
