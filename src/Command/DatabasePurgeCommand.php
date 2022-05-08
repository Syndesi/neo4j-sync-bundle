<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;

class DatabasePurgeCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:purge';

    private EntityManagerInterface $em;
    private Neo4jClientInterface $client;

    public function __construct(
        EntityManagerInterface $em,
        Neo4jClientInterface $client
    ) {
        $this->em = $em;
        $this->client = $client;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello world :D');

        return Command::SUCCESS;
    }
}
