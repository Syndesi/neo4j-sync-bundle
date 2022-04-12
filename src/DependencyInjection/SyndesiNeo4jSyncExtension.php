<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Syndesi\Neo4jSyncBundle\Contract\CreateType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidConfigurationException;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClientFactory;

class SyndesiNeo4jSyncExtension extends Extension
{
    /**
     * @throws InvalidConfigurationException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /**
         * @psalm-var array{
         *  clients: array{
         *    drivers: array{
         *      neo4j: array{
         *        url: ?string,
         *      },
         *      bolt: array{
         *        url: ?string,
         *      },
         *      http: array{
         *        url: ?string,
         *      },
         *    },
         *    default_driver: ?string,
         *  },
         *  default_client: ?string,
         *  page_size: int,
         *  use_merge_for_create_statements: bool,
         * }
         */
        $config = $this->parseConfig($configs, $container);

        $this->createClientServices($config, $container);

        $container->getDefinition('neo4j_sync.neo4j_statement_helper')->addArgument($config['page_size']);
        $container->getDefinition('neo4j_sync.command.db.sync')->addArgument($config['page_size']);

        $defaultCreateType = $config['use_merge_for_create_statements'] ? CreateType::MERGE : CreateType::CREATE;
        $container->getDefinition('neo4j_sync.neo4j_statement_helper')->addArgument($defaultCreateType);
    }

    private function parseConfig(array $configs, ContainerBuilder $container): array
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);

        return $this->processConfiguration($configuration, $configs);
    }

    private function createClientServices(array $config, ContainerBuilder $container)
    {
        // create client services
        foreach ($config['clients'] as $name => $clientConfig) {
            $serviceName = sprintf('neo4j_sync.neo4j_client.%s', $name);
            $definition = (new Definition(Neo4jClient::class, []))
                ->setFactory([Neo4jClientFactory::class, 'createClient'])
                ->addTag('monolog.logger', ['channel' => 'neo4j_sync'])
                ->addArgument($clientConfig);
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

    public function getAlias(): string
    {
        return 'neo4j_sync';
    }
}
