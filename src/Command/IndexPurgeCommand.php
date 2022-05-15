<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Laudis\Neo4j\Types\CypherMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedIndexNameException;
use Syndesi\Neo4jSyncBundle\Statement\DeleteIndexStatementBuilder;
use Syndesi\Neo4jSyncBundle\Statement\GetIndicesStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class IndexPurgeCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:index:purge';

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

    /**
     * @throws UnsupportedIndexNameException
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(("Purge all indices? (yes/NO): "), false);
        if (!$input->getOption('force')) {
            if (!$helper->ask($input, $output, $question)) {
                $io->writeln("Canceled execution");

                return Command::SUCCESS;
            }
        } else {
            $io->writeln("Skipped confirmation question due to force flag");
        }

        $io->writeln("Purging all indices...");
        $indices = $this->client->runStatement(GetIndicesStatementBuilder::build()[0]);
        foreach ($indices as $index) {
            /**
             * @var $index CypherMap
             */
            $indexVo = new Index(
                new IndexName($index->get('name')), // only name is used here, fill rest so that validator does not get triggered
                new NodeLabel('NotExistingNode'),
                [
                    new Property('id'),
                ],
                IndexType::BTREE
            );
            $this->client->addStatements(DeleteIndexStatementBuilder::build($indexVo));
        }
        $this->client->flush();
        $io->newLine();

        $io->success("Index purge completed");

        return Command::SUCCESS;
    }
}
