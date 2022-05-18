<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Event\DatabaseSyncEvent;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;

class DatabaseSyncCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:sync';

    public function __construct(
        private Neo4jClientInterface $client,
        private EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    protected function configure(): void
    {
        $this
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NEGATABLE,
                'Disables sanity check, executes without questions',
                false
            )
            ->addOption(
                'memory',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Memory limit in MB, useful if bigger datasets need to be synchronized'
            )
            ->addOption(
                'sync-type',
                null,
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    "How the data is synchronized.\n".
                    "MERGE: Slower, ensures that existing data is updated, no duplicated entries should be generated.\n".
                    "CREATE: Faster, use only on empty Neo4j database. Existing data might led to duplicated entries.\n".
                    "<options=bold>Notice</>: If command takes a long time, try using indices. If indices are already defined, check if they are already synced. Use %s\n",
                    IndexSyncCommand::getDefaultName()
                ),
                CreateType::MERGE->value
            )
        ;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->hasOption('memory')) {
            if ($input->getOption('memory')) {
                $memoryLimitInMegaBytes = intval($input->getOption('memory'));
                if ($memoryLimitInMegaBytes <= 0) {
                    throw new InvalidArgumentException("Memory limit must be greater than 0 MB");
                }
                $memoryLimitInBytes = $memoryLimitInMegaBytes * 1024 * 1024;
                ini_set('memory_limit', (string) $memoryLimitInBytes);
                $io->writeln(sprintf("Memory set to %d MB", $memoryLimitInMegaBytes));
            }
        }
        $syncType = $input->getOption('sync-type');
        if (!$syncType) {
            throw new InvalidArgumentException('sync-type can not be null');
        }
        $syncType = CreateType::from(strtoupper($syncType));

        $io->write("Loading data providers...");
        $event = new DatabaseSyncEvent(createType: $syncType);
        /**
         * @var DatabaseSyncEvent $databaseSyncEvent
         */
        $databaseSyncEvent = $this->eventDispatcher->dispatch($event, $event::NAME);
        $paginatedStatementProviders = $databaseSyncEvent->getPaginatedStatementProviders();
        $io->writeln(' done');

        $io->newLine();

        $io->writeln('The following providers were found:');
        $rows = [];
        foreach ($paginatedStatementProviders as $provider) {
            $rows[] = [
                get_class($provider),
                $provider->getName(),
                $provider->countElements(),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Provider', 'Content', 'Count'])
            ->setRows($rows)
        ;
        $table->render();

        $io->newLine();
        $io->writeln(sprintf("Sync type: %s", $syncType->value));

        $io->newLine();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(("Synchronize Neo4j database? (yes/NO): "), false);
        if (!$input->getOption('force')) {
            if (!$helper->ask($input, $output, $question)) {
                $io->writeln("Canceled execution");

                return Command::SUCCESS;
            }
        } else {
            $io->writeln("Skipped confirmation question due to force flag");
        }

        $io->newLine();

        $io->writeln('Synchronizing database...');
        $start = new DateTime();

        $allPages = 0;
        foreach ($paginatedStatementProviders as $paginatedStatementProvider) {
            $allPages += $paginatedStatementProvider->countPages();
        }
        $progressBar = new ProgressBar($output, $allPages);
        foreach ($paginatedStatementProviders as $paginatedStatementProvider) {
            foreach ($paginatedStatementProvider as $statements) {
                $this->client->addStatements($statements);
                $this->client->flush();
                $progressBar->advance();
            }
            $progressBar->clear();
            $io->writeln(sprintf("Synchronized %s with %s", get_class($paginatedStatementProvider), $paginatedStatementProvider->getName()));
            $progressBar->display();
        }
        $progressBar->finish();
        $progressBar->clear();
        $io->newLine();

        $io->success(
            sprintf(
                "Synchronization completed after %s (H:m:s)",
                $this->timeStringFromTimeDelta((new DateTime())->getTimestamp() - $start->getTimestamp())
            )
        );

        return Command::SUCCESS;
    }

    private function timeStringFromTimeDelta(int $timeDelta): string
    {
        $hours = floor($timeDelta / 3600);
        $minutes = floor(($timeDelta / 60) % 60);
        $seconds = $timeDelta % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}
