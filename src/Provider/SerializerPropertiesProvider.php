<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class SerializerPropertiesProvider implements PropertiesProviderInterface
{
    private Neo4jSerializer $serializer;

    public function __construct(
        private array $context = []
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ]);
    }

    /**
     * @returns Property[]
     *
     * @throws ExceptionInterface
     * @throws UnsupportedPropertyNameException
     */
    public function getProperties(object $entity): array
    {
        return $this->serializer->normalize($entity, null, $this->context);
    }
}
