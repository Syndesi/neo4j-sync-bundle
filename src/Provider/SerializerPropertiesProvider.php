<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\PropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class SerializerPropertiesProvider implements PropertiesProviderInterface
{
    private Neo4jSerializer $serializer;

    /**
     * @param array<array-key, mixed> $context
     */
    public function __construct(
        private array $context = [
            'group' => 'neo4j',
        ]
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
     * @throws InvalidArgumentException
     * @throws UnsupportedPropertyNameException
     */
    public function getProperties(?object $entity = null): array
    {
        if (!$entity) {
            throw new InvalidArgumentException('Entity must not be null');
        }

        $data = $this->serializer->normalize($entity, null, $this->context);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Serialized data is not an array.');
        }

        $properties = [];
        foreach ($data as $key => $value) {
            $properties[] = new Property($key, $value);
        }

        return $properties;
    }
}
