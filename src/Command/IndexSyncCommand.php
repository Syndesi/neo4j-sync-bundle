<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Event\GetAllIndicesEvent;
use Syndesi\Neo4jSyncBundle\Statement\CreateNodeIndexStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class IndexSyncCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:index:sync';

    public function __construct(
        private Neo4jClientInterface $client,
        private EventDispatcherInterface $eventDispatcher
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

        $event = new GetAllIndicesEvent();
        /**
         * @var GetAllIndicesEvent $getAllIndicesEvent
         */
        $getAllIndicesEvent = $this->eventDispatcher->dispatch($event, $event::NAME);
        $indices = $getAllIndicesEvent->getIndices();

        $io->writeln(sprintf("Found %d indices:", count($indices)));
        $rows = [];
        foreach ($indices as $index) {
            $propertiesString = [];
            foreach ($index->getProperties() as $property) {
                $propertiesString[] = $property->getName();
            }
            $propertiesString = implode($propertiesString);
            $rows[(string) $index->getName()] = [
                (string) $index->getName(),
                sprintf(
                    "%s (%s)",
                    (string) $index->getLabel(),
                    $index->getLabel() instanceof NodeLabel ? 'Node' : 'Relation'
                ),
                $index->getType()->value,
                $propertiesString,
            ];
        }
        ksort($rows);
        $table = new Table($output);
        $table
            ->setHeaders(['Name', 'On', 'Type', 'Properties'])
            ->setRows($rows);
        $table->render();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(sprintf("Continue and create %d indices? (yes/NO): ", count($indices)), false);
        if (!$input->getOption('force')) {
            if (!$helper->ask($input, $output, $question)) {
                $io->writeln("Canceled execution");

                return Command::SUCCESS;
            }
        } else {
            $io->writeln("Skipped confirmation question due to force flag");
        }

        $io->write('Creating indices...');
        foreach ($indices as $index) {
            $this->client->addStatements(CreateNodeIndexStatementBuilder::build($index));
        }
        $this->client->flush();
        $io->writeln(' done');

        return Command::SUCCESS;
    }
}
