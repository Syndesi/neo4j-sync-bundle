<?php

namespace Syndesi\Neo4jSyncBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Syndesi\Neo4jSyncBundle\Contract\NormalizerProviderInterface;

class ObjectNormalizerProvider implements NormalizerProviderInterface
{
    public function getNormalizer(): NormalizerInterface
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        return new ObjectNormalizer($classMetadataFactory);
    }
}
