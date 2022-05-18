<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use DateTime;
use Laudis\Neo4j\Databags\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Statement\DeleteAllNodesAndRelationsLimitedStatementBuilder;

class DatabasePurgeCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:purge';

    public function __construct(
        private Neo4jClientInterface $client
    ) {
        parent::__construct();
    }

    protected function configure(): void
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

        $io->newLine();

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

        $io->newLine();

        $start = new DateTime();
        $io->writeln("Purging all nodes and relations...");
        $count = $this->getRemainingNodes();
        $progressBar = new ProgressBar($output, (int) ceil($count / DeleteAllNodesAndRelationsLimitedStatementBuilder::LIMIT));
        while ($count > 0) {
            $this->client->addStatements(DeleteAllNodesAndRelationsLimitedStatementBuilder::build());
            $this->client->flush();
            $count = $this->getRemainingNodes();
            $progressBar->advance();
        }
        $progressBar->finish();
        $progressBar->clear();

        $io->newLine();

        $io->success(
            sprintf(
                "Database purge completed after %s (H:m:s)",
                $this->timeStringFromTimeDelta((new DateTime())->getTimestamp() - $start->getTimestamp())
            )
        );
        /**
         * @psalm-suppress PossiblyNullArgument
         */
        $io->writeln(sprintf("To purge indices please run command \"%s\"", IndexPurgeCommand::getDefaultName()));

        return Command::SUCCESS;
    }

    private function getRemainingNodes(): int
    {
        $res = $this->client->runStatement(new Statement('MATCH (n) RETURN count(n) as count', []));

        return $res->get(0)->get('count');
    }

    private function timeStringFromTimeDelta(int $timeDelta): string
    {
        $hours = floor($timeDelta / 3600);
        $minutes = floor(($timeDelta / 60) % 60);
        $seconds = $timeDelta % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}
