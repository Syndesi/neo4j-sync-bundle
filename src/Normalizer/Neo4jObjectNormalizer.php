<?php

namespace Syndesi\Neo4jSyncBundle\Normalizer;

use DateInterval;
use DateTimeInterface;
use Laudis\Neo4j\Types as Neo4j;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Neo4jObjectNormalizer implements NormalizerInterface
{
    public const SUPPORTED_CLASSES = [
        DateTimeInterface::class,
        DateInterval::class,
        Neo4j\Date::class,
        Neo4j\Time::class,
        Neo4j\LocalTime::class,
        Neo4j\DateTime::class,
        Neo4j\DateTimeZoneId::class,
        Neo4j\LocalDateTime::class,
        Neo4j\Duration::class,
        Neo4j\Node::class,
        Neo4j\Relationship::class,
        Neo4j\Path::class,
    ];

    /**
     * @throws InvalidArgumentException
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
        Neo4j\Path
    {
        foreach (self::SUPPORTED_CLASSES as $supportedClass) {
            if ($object instanceof $supportedClass) {
                return $object;
            }
        }
        throw new InvalidArgumentException(sprintf('The object must implement one of the following classes: %s.', implode(', ', self::SUPPORTED_CLASSES)));
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        foreach (self::SUPPORTED_CLASSES as $allowedClass) {
            if ($data instanceof $allowedClass) {
                return true;
            }
        }

        return false;
    }
}
