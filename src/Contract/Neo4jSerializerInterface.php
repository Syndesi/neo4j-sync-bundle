<?php

namespace Syndesi\Neo4jSyncBundle\Contract;

use ArrayObject;
use DateInterval;
use DateTimeInterface;
use Laudis\Neo4j\Types as Neo4j;

interface Neo4jSerializerInterface
{
    public function normalize(mixed $data, string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null|
        DateTimeInterface|
        DateInterval|
        Neo4j\Date|
        Neo4j\Time|
        Neo4j\LocalTime|
        Neo4j\DateTime|
        Neo4j\DateTimeZoneId|
        Neo4j\LocalDateTime|
        Neo4j\Duration|
        Neo4j\Node|
        Neo4j\Relationship|
        Neo4j\Path;
}
