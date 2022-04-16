<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedNodeLabelException;

class NodeLabel implements Stringable
{
    public const NODE_LABEL_REGEX = '([A-Z][a-z0-9]+){2,}';

    /**
     * @throws UnsupportedNodeLabelException
     */
    public function __construct(
        private readonly string $label
    ) {
        if (!preg_match(self::NODE_LABEL_REGEX, $label)) {
            throw new UnsupportedNodeLabelException(sprintf("Node label '%s' does not match Regex '%s'.", $label, self::NODE_LABEL_REGEX));
        }
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function __toString()
    {
        return $this->label;
    }
}
