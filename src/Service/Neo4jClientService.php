<?php
namespace Syndesi\Neo4jSyncBundle\Service;

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class Neo4jClientService {

    private ClientInterface $client;

    public function __construct (array $drivers) {
        $clientBuilder = ClientBuilder::create();
        $defaultDriver = null;
        if ($drivers['bolt']['url']) {
            $clientBuilder = $clientBuilder->withDriver(
                'bolt',
                $drivers['bolt']['url']
            );
            $defaultDriver = 'bolt';
        }
        if ($drivers['https']['url']) {
            $clientBuilder = $clientBuilder->withDriver(
                'https',
                $drivers['https']['url'],
                Authenticate::basic(
                    $drivers['https']['authentication']['user'],
                    $drivers['https']['authentication']['password']
                )
            );
            if (!$defaultDriver) {
                $defaultDriver = 'https';
            }
        }
        $clientBuilder = $clientBuilder->withDefaultDriver($defaultDriver);
        $this->client = $clientBuilder->build();
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
