<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

enum CreateType: string
{
    /*
     * See also https://neo4j.com/docs/cypher-manual/current/clauses/create/
     */
    case CREATE = 'CREATE';
    /*
     * See also https://neo4j.com/docs/cypher-manual/current/clauses/merge/
     */
    case MERGE = 'MERGE';
}
