<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$this->eventDispatcher) {
            $io->error('No event dispatcher found');

            return Command::FAILURE;
        }

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
                    $index->getLabel(),
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

        // ask user if indices should be created

        foreach ($indices as $index) {
            $this->client->addStatements(CreateNodeIndexStatementBuilder::build($index));
        }
        $this->client->flush();

        return Command::SUCCESS;
    }
}
