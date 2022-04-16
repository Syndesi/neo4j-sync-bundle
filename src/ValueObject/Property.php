<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;

class Property
{
    public const PROPERTY_NAME_REGEX = '/^[a-z][a-zA-Z0-9_]+$/';

    /**
     * @throws UnsupportedPropertyNameException
     */
    public function __construct(
        private readonly string $name,
        private readonly mixed $value = null
    ) {
        if (!preg_match(self::PROPERTY_NAME_REGEX, $name)) {
            throw new UnsupportedPropertyNameException(sprintf("Property name '%s' does not match Regex '%s'.", $name, self::PROPERTY_NAME_REGEX));
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
