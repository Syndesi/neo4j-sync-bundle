<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;
use Syndesi\Neo4jSyncBundle\Enum\DriverType;

class Driver implements Stringable, IsEqualToInterface
{
    public function __construct(
        private readonly DriverType $type,
        private readonly string $url
    ) {
    }

    public function getType(): DriverType
    {
        return $this->type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function __toString()
    {
        return sprintf("%s: %s", $this->type->value, $this->url);
    }

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof Driver)) {
            return false;
        }

        return
            $this->type === $element->type &&
            $this->url === $element->url;
    }
}
