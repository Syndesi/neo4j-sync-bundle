<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use ArrayObject;
use DateInterval;
use DateTimeInterface;
use Laudis\Neo4j\Types as Neo4j;

interface Neo4jSerializerInterface
{
    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<array-key, mixed> $context
     * @return array<array-key, mixed>|string|int|float|bool|ArrayObject|DateTimeInterface|DateInterval|Neo4j\Date|Neo4j\DateTime|Neo4j\DateTimeZoneId|Neo4j\LocalDateTime|Neo4j\Duration|Neo4j\Node|Neo4j\Relationship|null
     */
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
