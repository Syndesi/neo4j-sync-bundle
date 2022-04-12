<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Syndesi\Neo4jSyncBundle\Contract\Neo4jSerializerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NormalizerProviderInterface;
use Syndesi\Neo4jSyncBundle\Serializer\Neo4jSerializer;

class Neo4jNormalizer
{
    private Neo4jSerializerInterface $serializer;

    /**
     * @param NormalizerProviderInterface[] $normalizerProviders
     */
    public function __construct(array $normalizerProviders)
    {
        $normalizers = [];
        foreach ($normalizerProviders as $provider) {
            $normalizers[] = $provider->getNormalizer();
        }
        $this->serializer = new Neo4jSerializer($normalizers);
    }

    /**
     * @throws
     */
    public function normalize(mixed $data, string $format = null, array $context = []): array
    {
        return $this->serializer->normalize($data, $format, $context);
    }
}
