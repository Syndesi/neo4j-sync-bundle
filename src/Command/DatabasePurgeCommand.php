<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Statement\DeleteAllNodesAndRelationsStatementBuilder;

class DatabasePurgeCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:purge';

    public function __construct(
        private Neo4jClientInterface $client
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

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(("Purge all nodes and relations? (yes/NO): "), false);
        if (!$input->getOption('force')) {
            if (!$helper->ask($input, $output, $question)) {
                $io->writeln("Canceled execution");

                return Command::SUCCESS;
            }
        } else {
            $io->writeln("Skipped confirmation question due to force flag");
        }

        $io->write("Purging all nodes and relations...");
        $this->client->addStatements(DeleteAllNodesAndRelationsStatementBuilder::build());
        $this->client->flush();
        $io->writeln(" done");

        $io->newLine();
        $io->writeln(sprintf("To purge indices please run command \"%s\"", IndexPurgeCommand::getDefaultName()));

        return Command::SUCCESS;
    }
}
