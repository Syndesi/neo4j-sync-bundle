<?php

namespace Syndesi\Neo4jSyncBundle\Command;

use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Types\CypherMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;

class DatabaseDeleteCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:delete';
    private Neo4jClient $client;

    public function __construct(Neo4jClient $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Deletes all data and indices from the Neo4j database.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NEGATABLE,
                'Option to disable manual sanity check.',
                false
            )
            ->addOption(
                'withIndices',
                null,
                InputOption::VALUE_NEGATABLE,
                'If true indices are also deleted',
                false
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // sanity check
        if (!$input->getOption('force')) {
            $question = new ConfirmationQuestion('Are you sure to delete the database (data & indices)? (y/N) ', false);
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $output->writeln('');
            if (!$answer) {
                $output->writeln('Stopped command');

                return Command::SUCCESS;
            }
        }

        // generate delete statements for all indices
        if ($input->getOption('withIndices')) {
            $statement = new Statement('SHOW INDEXES', []);
            $res = $this->client->getClient()->runStatement($statement);
            /**
             * @var $element CypherMap
             */
            foreach ($res->toArray() as $element) {
                $this->client->addStatement(
                    new Statement(sprintf('DROP INDEX %s', $element->get('name')), [])
                );
            }
            $output->writeln(sprintf('Added delete statements for %d indices', count($res->toArray())));
        }

        // generate delete statement for data
        $statement = new Statement('MATCH (n) DETACH DELETE n', []);
        $this->client->addStatement($statement);
        $output->writeln('Added delete statement for data');

        // running all statements
        $output->write('Running delete statements... ');
        $this->client->flush();
        $output->writeln('done');

        return Command::SUCCESS;
    }
}
