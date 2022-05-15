<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Enum;

enum DriverType: string
{
    case NEO4J = 'neo4j';
    case BOLT = 'bolt';
    case HTTP = 'http';
}
