<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\IdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedPropertyNameException;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class SerializerIdentifierProvider implements IdentifierProviderInterface
{
    private Neo4jSerializer $serializer;

    public function __construct(
        private readonly Property $identifier,
        private array $context = [
            'group' => 'neo4j-relation',
        ]
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizer($classMetadataFactory),
        ]);
    }

    /**
     * @throws ExceptionInterface
     * @throws UnsupportedPropertyNameException
     */
    public function getIdentifier(object $entity): Property
    {
        $data = $this->serializer->normalize($entity, null, $this->context);

        return new Property($this->identifier->getName(), $data[$this->identifier->getValue()]);
    }
}
