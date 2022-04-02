<?php
namespace Syndesi\Neo4jSyncBundle\Enum;

enum Index: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
