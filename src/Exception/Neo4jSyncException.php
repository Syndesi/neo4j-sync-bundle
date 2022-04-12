<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Exception;

use Exception;
use Throwable;

class Neo4jSyncException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
