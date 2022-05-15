<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Syndesi\Neo4jSyncBundle\Enum\DriverType;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('neo4j_sync');
        $rootNode = $treeBuilder->getRootNode();

        $driverTypes = [];
        foreach (DriverType::cases() as $type) {
            $driverTypes[] = $type->value;
        }
        $driverTypes = implode(', ', $driverTypes);

        $rootNode
            ->children()
            ->arrayNode('clients')
                ->arrayPrototype()
                    ->info(
                        "Name of the client, usually 'default'.\n".
                        "Note: Do not use lists, aka '-'."
                    )
                    ->children()
                        ->arrayNode('drivers')
                            ->info('See also https://github.com/neo4j-php/neo4j-php-client#step-2-create-a-client.')

                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('type')
                                        ->info(sprintf("Type of the driver, can be one of %s", $driverTypes))
                                        ->defaultValue(DriverType::BOLT->value)
                                    ->end()
                                    ->scalarNode('url')
                                        ->info('Url for the driver')
                                    ->end()
                                ->end()
                            ->end()

//                            ->children()
//                                ->arrayNode('neo4j')
//                                    ->info("Configuration for Laudi's neo4j client driver.")
//                                    ->children()
//                                        ->scalarNode('url')
//                                            ->defaultNull()
//                                            ->info('neo4j://user:password@neo4j.test.com?database=my-database')
//                                        ->end()
//                                    ->end()
//                                ->end()
//                                ->arrayNode('bolt')
//                                    ->info("Configuration for Laudi's bolt client driver.")
//                                    ->children()
//                                        ->scalarNode('url')
//                                            ->defaultNull()
//                                            ->info('bolt+s://user:password@test.com')
//                                        ->end()
//                                    ->end()
//                                ->end()
//                                ->arrayNode('http')
//                                    ->info("Configuration for Laudi's http client driver.")
//                                    ->children()
//                                        ->scalarNode('url')
//                                            ->defaultNull()
//                                            ->info('https://user:password@test.com')
//                                        ->end()
//                                    ->end()
//                                ->end()
//                            ->end()
                        ->end()
                        ->scalarNode('default_driver')
                            ->defaultNull()
                            ->info(
                                "Name of the driver which should be used by default.\n".
                                'If not set or null, then the first driver is used as default driver.'
                            )
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('default_client')
                ->defaultNull()
                ->info(
                    "Name of client which is used by default.\n".
                    'If not set or null, then the first client is used as default client.'
                )
            ->end()
            ->scalarNode('page_size')
                ->defaultValue(250)
                ->info(
                    'Determines how many elements are used within every Neo4j batch request.'
                )
            ->end()
            ->booleanNode('disable_doctrine_listeners')
                ->defaultFalse()
                ->info(
                    'Disables doctrine listeners, useful for local development'
                )
            ->end()
        ->end()
        ->end();

        return $treeBuilder;
    }
}
