<?php

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('syndesi_neo4j_sync');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()

            ->arrayNode('neo4j_drivers')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->info('See also https://github.com/neo4j-php/neo4j-php-client#step-2-create-a-client')
                ->children()
                    ->arrayNode('bolt')
                        ->ignoreExtraKeys()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url')
                                ->defaultNull()
                                ->info('bolt+s://user:password@localhost')
                                ->end()
                        ->end()
                    ->end()
                    ->arrayNode('https')
                        ->ignoreExtraKeys()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url')
                                ->defaultNull()
                                ->info('https://test.com')
                                ->end()
                            ->arrayNode('authentication')
                                ->ignoreExtraKeys()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('user')
                                        ->defaultNull()
                                        ->info('user')
                                        ->end()
                                    ->scalarNode('password')
                                        ->defaultNull()
                                        ->info('password')
                                        ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
