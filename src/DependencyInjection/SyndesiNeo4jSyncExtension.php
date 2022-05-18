<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Syndesi\Neo4jSyncBundle\Exception\InvalidConfigurationException;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClientFactory;

class SyndesiNeo4jSyncExtension extends Extension
{
    /**
     * @param array<array-key, mixed> $configs
     *@throws InvalidConfigurationException
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->parseConfig($configs, $container);

        $this->createClientServices($config, $container);
        $this->handleDisableDoctrineListeners($container, $config['disable_doctrine_listeners']);
    }

    /**
     * @param array<array-key, mixed> $configs
     * @return array<array-key, mixed>
     * @throws Exception
     */
    private function parseConfig(array $configs, ContainerBuilder $container): array
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        if (!$configuration) {
            throw new InvalidConfigurationException('Parsing of configuration failed');
        }

        return $this->processConfiguration($configuration, $configs);
    }

    /**
     * @param array<array-key, mixed> $config
     * @throws InvalidConfigurationException
     */
    private function createClientServices(array $config, ContainerBuilder $container): void
    {
        // create client services
        foreach ($config['clients'] as $name => $clientConfig) {
            $serviceName = sprintf('neo4j_sync.neo4j_client.%s', $name);
            $definition = (new Definition(Neo4jClient::class, []))
                ->setFactory([Neo4jClientFactory::class, 'createClient'])
                ->addTag('monolog.logger', ['channel' => 'neo4j_sync'])
                ->addArgument($clientConfig)
            ;
            $container->setDefinition($serviceName, $definition);
        }

        // set default client
        if (!array_key_exists('default_client', $config)) {
            throw new InvalidConfigurationException('Missing configuration default_client');
        }
        $defaultClient = array_keys($config['clients'])[0];
        if (null !== $config['default_client']) {
            $defaultClient = $config['default_client'];
            if (!array_key_exists($defaultClient, $config['clients'])) {
                throw new InvalidConfigurationException(sprintf("Did not found default client with name '%s' under configured clients", $defaultClient));
            }
        }
        $defaultClient = $container->getDefinition(sprintf('neo4j_sync.neo4j_client.%s', $defaultClient));
        $container->setDefinition('neo4j_sync.neo4j_client', $defaultClient);
    }

    private function handleDisableDoctrineListeners(ContainerBuilder $container, bool $disableDoctrineListeners = false): void
    {
        $listeners = [
            'neo4j_sync.event_listener.doctrine_post_flush_subscriber',
            'neo4j_sync.event_listener.doctrine_post_persist_node_subscriber',
            'neo4j_sync.event_listener.doctrine_post_persist_relation_subscriber',
            'neo4j_sync.event_listener.doctrine_post_update_node_subscriber',
            'neo4j_sync.event_listener.doctrine_post_update_relation_subscriber',
            'neo4j_sync.event_listener.doctrine_pre_remove_node_subscriber',
            'neo4j_sync.event_listener.doctrine_pre_remove_relation_subscriber',
        ];
        foreach ($listeners as $listener) {
            $container->getDefinition($listener)
                ->addArgument($disableDoctrineListeners);
        }
    }

    public function getAlias(): string
    {
        return 'neo4j_sync';
    }
}
