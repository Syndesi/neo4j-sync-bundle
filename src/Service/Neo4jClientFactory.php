<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\ClientBuilder;
use Psr\Log\LoggerInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidConfigurationException;

class Neo4jClientFactory
{
    /**
     * @throws InvalidConfigurationException
     */
    public static function createClient(array $config, LoggerInterface $logger): Neo4jClient
    {
        if (!array_key_exists('drivers', $config)) {
            throw new InvalidConfigurationException('Missing configuration client.drivers');
        }
        $clientBuilder = ClientBuilder::create();

        // add drivers
        foreach ($config['drivers'] as $driver => $driverConfig) {
            if (!array_key_exists('url', $driverConfig)) {
                throw new InvalidConfigurationException(sprintf('Missing configuration client.drivers.%s.url', $driver));
            }
            $clientBuilder = $clientBuilder->withDriver($driver, $driverConfig['url']);
        }

        // set default driver
        $defaultDriver = array_keys($config['drivers'])[0];
        if (!array_key_exists('default_driver', $config)) {
            throw new InvalidConfigurationException('Missing configuration client.default_driver');
        }
        if (null !== $config['default_driver']) {
            $defaultDriver = $config['default_driver'];
            if (!array_key_exists($defaultDriver, $config['drivers'])) {
                throw new InvalidConfigurationException(sprintf("Did not found default driver with name '%s' under configured drivers", $config['default_driver']));
            }
        }
        $clientBuilder = $clientBuilder->withDefaultDriver($defaultDriver);

        // create client
        $client = $clientBuilder->build();

        return new Neo4jClient($client, $logger);
    }
}
