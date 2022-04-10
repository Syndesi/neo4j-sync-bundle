<?php

namespace Syndesi\Neo4jSyncBundle\Command;

use Laudis\Neo4j\Databags\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;

class TestCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:test';
    private Neo4jClient $client;

    public function __construct(Neo4jClient $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello world :D');

//        print_r($this->client);

        $statement = new Statement('MATCH (n) DETACH DELETE n', []);
        $this->client->addStatement($statement);
        $this->client->flush();

        return Command::SUCCESS;
    }
}
