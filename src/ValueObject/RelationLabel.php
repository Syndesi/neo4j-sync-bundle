<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\LabelInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedRelationLabelException;

class RelationLabel implements Stringable, LabelInterface
{
    public const RELATION_LABEL_REGEX = '/^[A-Z]+(_[A-Z]+)*$/';

    /**
     * @param string $label Label of the relationship
     *
     * @throws UnsupportedRelationLabelException
     */
    public function __construct(
        private readonly string $label
    ) {
        if (!preg_match(self::RELATION_LABEL_REGEX, $label)) {
            throw new UnsupportedRelationLabelException(sprintf("Relation label '%s' does not match Regex '%s'.", $label, self::RELATION_LABEL_REGEX));
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
