<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\ClientBuilder;
use Psr\Log\LoggerInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidConfigurationException;
use Syndesi\Neo4jSyncBundle\ValueObject\Client;

class Neo4jClientFactory
{
    /**
     * @throws InvalidConfigurationException
     */
    public static function createClient(Client $client, LoggerInterface $logger): Neo4jClient
    {
        $clientBuilder = ClientBuilder::create();

        // add drivers
        foreach ($client->getDrivers() as $driverName => $driverConfig) {
            $clientBuilder = $clientBuilder->withDriver($driverName, $driverConfig->getUrl());
        }

        // set default driver
        $clientBuilder = $clientBuilder->withDefaultDriver($client->getDefaultDriver());

        // create client
        $client = $clientBuilder->build();

        return new Neo4jClient($client, $logger);
    }
}
