<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jSerializerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;
use Syndesi\Neo4jSyncBundle\Service\ObjectNormalizerProvider;

class SerializerNodePropertiesProvider implements NodePropertiesProviderInterface
{
    private readonly Neo4jSerializerInterface $serializer;

    public function __construct(
        private readonly array $context = []
    ) {
        $this->serializer = new Neo4jSerializer([
            new Neo4jObjectNormalizer(),
            new ObjectNormalizerProvider(),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function getNodeProperties(object $entity): array
    {
        return $this->serializer->normalize($entity, null, $this->context);
    }
}
