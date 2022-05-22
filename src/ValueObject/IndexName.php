<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedIndexNameException;

class IndexName implements Stringable, IsEqualToInterface
{
    public const INDEX_REGEX = '/^[a-z][a-z\d_]*$/';

    /**
     * @throws UnsupportedIndexNameException
     */
    public function __construct(
        private readonly string $name
    ) {
        if (!preg_match(self::INDEX_REGEX, $name)) {
            throw new UnsupportedIndexNameException(sprintf("Index name '%s' does not match Regex '%s'.", $name, self::INDEX_REGEX));
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof IndexName)) {
            return false;
        }

        return $this->name === $element->name;
    }
}
