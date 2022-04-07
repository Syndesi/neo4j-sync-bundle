<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Normalizer\RamseyUuidNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;

class Neo4jNormalizer
{
    private Neo4jSerializer $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $neo4jObjectNormalizer = new Neo4jObjectNormalizer();
        $ramseyUuidNormalizer = new RamseyUuidNormalizer();
        $this->serializer = new Neo4jSerializer([$neo4jObjectNormalizer, $ramseyUuidNormalizer, $normalizer]);
    }

    /**
     * @throws
     */
    public function normalize(mixed $data, string $format = null, array $context = []): array
    {
        return $this->serializer->normalize($data, $format, $context);
    }
}
