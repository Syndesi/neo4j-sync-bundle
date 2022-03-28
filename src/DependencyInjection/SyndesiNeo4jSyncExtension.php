<?php

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SyndesiNeo4jSyncExtension extends Extension
{
    /**
     * @throws
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('syndesi_neo4j_sync.neo4j_client_service');
        $definition->setArgument(0, $config['neo4j_drivers']);
    }

    public function getAlias(): string
    {
        return 'syndesi_neo4j_sync';
    }
}
