<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Event\DatabaseSyncEvent;

class DatabaseSyncCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:sync';

    public function __construct(
        private Neo4jClientInterface $client,
        private EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NEGATABLE,
                'Disables sanity check, executes without questions',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$this->eventDispatcher) {
            $io->error('No event dispatcher found');

            return Command::FAILURE;
        }

        $event = new DatabaseSyncEvent(createType: CreateType::MERGE);
        /**
         * @var DatabaseSyncEvent $databaseSyncEvent
         */
        $databaseSyncEvent = $this->eventDispatcher->dispatch($event, $event::NAME);
        $paginatedStatementProviders = $databaseSyncEvent->getPaginatedStatementProviders();

        $io->writeln(sprintf("count providers: %d", count($paginatedStatementProviders)));

        foreach ($paginatedStatementProviders as $paginatedStatementProvider) {
            foreach ($paginatedStatementProvider as $key => $statements) {
                $io->write('.');
                $this->client->addStatements($statements);
                $this->client->flush();
            }
        }
        $io->writeln('finished :D');

        return Command::SUCCESS;
    }
}
