<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jSerializerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class SerializerNodePropertiesProvider implements NodePropertiesProviderInterface
{
    private readonly Neo4jSerializerInterface $serializer;

    public function __construct(
        private readonly array $context = []
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ]);
    }

    /**
     * @return Property[]
     *
     * @throws UnsupportedPropertyNameException
     * @throws ExceptionInterface
     * @throws UnsupportedPropertyNameException
     */
    public function getNodeProperties(object $entity): array
    {
        $data = $this->serializer->normalize($entity, null, $this->context);
        $propertiesArray = [];
        foreach ($data as $name => $value) {
            $propertiesArray[] = new Property($name, $value);
        }

        return $propertiesArray;
    }
}
