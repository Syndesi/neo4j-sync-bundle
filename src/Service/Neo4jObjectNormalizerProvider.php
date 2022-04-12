<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Syndesi\Neo4jSyncBundle\Contract\NormalizerProviderInterface;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;

class Neo4jObjectNormalizerProvider implements NormalizerProviderInterface
{
    public function getNormalizer(): NormalizerInterface
    {
        return new Neo4jObjectNormalizer();
    }
}
