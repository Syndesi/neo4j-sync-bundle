<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Helper;

use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;

class CompareEqualHelper
{
    public static function compare(?IsEqualToInterface $a, ?IsEqualToInterface $b): bool
    {
        if ($a === null && $b === null) {
            return true;
        }
        /** @psalm-suppress RedundantCondition */
        if ($a === null && $b) {
            return false;
        }
        if ($a && $b === null) {
            return false;
        }
        // a and b must be not null
        /** @psalm-suppress PossiblyNullArgument */
        if (get_class($a) !== get_class($b)) {
            return false;
        }

        return $a->isEqualTo($b);
    }
}
