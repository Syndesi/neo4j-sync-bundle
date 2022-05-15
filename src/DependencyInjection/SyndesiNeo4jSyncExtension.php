<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Syndesi\Neo4jSyncBundle\Enum\DriverType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidConfigurationException;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClientFactory;
use Syndesi\Neo4jSyncBundle\ValueObject\Client;
use Syndesi\Neo4jSyncBundle\ValueObject\Configuration as ConfigurationVO;
use Syndesi\Neo4jSyncBundle\ValueObject\Driver;

class SyndesiNeo4jSyncExtension extends Extension
{
    /**
     * @throws
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->parseConfig($configs, $container);

        $this->createClientServices($config, $container);
        $this->handleDisableDoctrineListeners($container, $config->isDisableDoctrineListeners());
    }

    /**
     * @throws Exception
     */
    private function parseConfig(array $configs, ContainerBuilder $container): ConfigurationVO
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $clients = [];
        foreach ($config['clients'] as $name => $clientConfig) {
            $drivers = [];
            foreach ($clientConfig['drivers'] as $driverName => $driverConfig) {
                $drivers[$driverName] = new Driver(
                    DriverType::from($driverConfig['type']),
                    $driverConfig['url']
                );
            }
            $clients[$name] = new Client(
                $drivers,
                $clientConfig['default_driver'] ?: array_keys($clientConfig['drivers'])[0]
            );
        }

        return new ConfigurationVO(
            $clients,
            $config['default_client'] ?: array_keys($config['clients'])[0],
            $config['page_size'],
            $config['disable_doctrine_listeners']
        );
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function createClientServices(ConfigurationVO $config, ContainerBuilder $container)
    {
        // create client services
        foreach ($config->getClients() as $name => $clientConfig) {
            $serviceName = sprintf('neo4j_sync.neo4j_client.%s', $name);
            $definition = (new Definition(Neo4jClient::class, []))
                ->setFactory([Neo4jClientFactory::class, 'createClient'])
                ->addTag('monolog.logger', ['channel' => 'neo4j_sync'])
//                ->addArgument($clientConfig)
                ->setArgument('$client', [$clientConfig])
            ;
            $container->setDefinition($serviceName, $definition);
        }

        // set default client
        $defaultClient = $container->getDefinition(sprintf('neo4j_sync.neo4j_client.%s', $clientConfig->getDefaultDriver()));
        $container->setDefinition('neo4j_sync.neo4j_client', $defaultClient);
    }

    private function handleDisableDoctrineListeners(ContainerBuilder $container, bool $disableDoctrineListeners = false)
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
