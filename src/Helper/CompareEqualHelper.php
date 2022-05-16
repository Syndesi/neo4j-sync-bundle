<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Helper;

use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;

class CompareEqualHelper
{
    public static function compare(?IsEqualToInterface $a, ?IsEqualToInterface $b): bool
    {
        if (!$a && !$b) {
            return true;
        }
        if (!$a && $b) {
            return false;
        }
        if ($a && !$b) {
            return false;
        }
        // a and b must be not null
        if (get_class($a) !== get_class($b)) {
            return false;
        }

        return $a->isEqualTo($b);
    }
}
