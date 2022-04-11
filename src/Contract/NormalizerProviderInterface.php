<?php

namespace Syndesi\Neo4jSyncBundle\Contract;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface NormalizerProviderInterface
{
    public function getNormalizer(): NormalizerInterface;
}
