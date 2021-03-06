<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\LabelInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedNodeLabelException;

class NodeLabel implements Stringable, LabelInterface
{
    public const NODE_LABEL_REGEX = '/^[A-Z][a-zA-Z0-9]*$/';

    /**
     * @param string $label Label of the node, e.g. DemoLabel.
     *
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

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof NodeLabel)) {
            return false;
        }

        return $this->label === $element->label;
    }
}
