<?php

namespace Syndesi\Neo4jSyncBundle\Contract;

enum IndexType: string
{
    case ALL = 'ALL';
    case BTREE = 'BTREE';
    case FULLTEXT = 'FULLTEXT';
    case LOOKUP = 'LOOKUP';
    case POINT = 'POINT';
    case RANGE = 'RANGE';
    case TEXT = 'TEXT';
}
