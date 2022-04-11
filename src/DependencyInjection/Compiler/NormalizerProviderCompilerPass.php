<?php

namespace Syndesi\Neo4jSyncBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NormalizerProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // todo are the references already sorted, due to priority?
        $definition = $container->getDefinition('neo4j_sync.neo4j_normalizer');
        $references = [];
        foreach ($container->findTaggedServiceIds('neo4j_sync_normalizer_provider') as $id => $tags) {
            $priority = $tags[0]['priority'];
            if (!array_key_exists($priority, $references)) {
                $references[$priority] = [];
            }
            $references[$priority][] = new Reference($id);
        }
        krsort($references);
        $flattenedReferences = [];
        foreach ($references as $tmpReferences) {
            $flattenedReferences = array_merge($flattenedReferences, $tmpReferences);
        }
        $definition->setArgument(0, $flattenedReferences);
    }
}
