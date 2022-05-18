<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Contract;

use DateInterval;
use DateTimeInterface;
use Laudis\Neo4j\Types as Neo4j;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface Neo4jNormalizerInterface extends NormalizerInterface
{
    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     *
     * @return DateTimeInterface|DateInterval|Neo4j\Date|Neo4j\Time|Neo4j\LocalTime|Neo4j\DateTime|Neo4j\DateTimeZoneId|Neo4j\LocalDateTime|Neo4j\Duration|Neo4j\Node|Neo4j\Relationship|Neo4j\Path some text so that php-cs-fixer doesn't remove this line
     */
    public function normalize(mixed $object, string $format = null, array $context = []): DateTimeInterface|
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
