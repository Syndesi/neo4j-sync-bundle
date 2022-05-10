<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Laudis\Neo4j\Types\CypherMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Enum\IndexType;
use Syndesi\Neo4jSyncBundle\Event\GetAllIndicesEvent;
use Syndesi\Neo4jSyncBundle\Exception\Neo4jSyncException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedNodeLabelException;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedRelationLabelException;
use Syndesi\Neo4jSyncBundle\Statement\GetIndicesStatementBuilder;
use Syndesi\Neo4jSyncBundle\ValueObject\Index;
use Syndesi\Neo4jSyncBundle\ValueObject\IndexName;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;
use Syndesi\Neo4jSyncBundle\ValueObject\RelationLabel;

class IndexListCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:index:list';

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

        $existingIndices = $this->getNeo4jIndices();

        $event = new GetAllIndicesEvent();
        /**
         * @var $getAllIndicesEvent GetAllIndicesEvent
         */
        $getAllIndicesEvent = $this->eventDispatcher->dispatch($event, $event::NAME);
        $definedIndices = $getAllIndicesEvent->getIndices();

        $rowElements = [];
        foreach ($definedIndices as $index) {
            /*
             * @var $index Index
             */
            $rowElements[(string) $index->getName()] = [
                'index' => $index,
                'defined' => true,
                'exists' => false,
                'conflict' => false,
            ];
        }
        foreach ($existingIndices as $index) {
            /**
             * @var $index Index
             */
            if (array_key_exists((string) $index->getName(), $rowElements)) {
                if ($index->isEqualTo($rowElements[(string) $index->getName()]['index'])) {
                    $rowElements[(string) $index->getName()]['exists'] = true;
                } else {
                    $rowElements[(string) $index->getName()]['conflict'] = true;
                    $rowElements[sprintf("%s (database)", $index->getName())] = [
                        'index' => $index,
                        'defined' => false,
                        'exists' => true,
                        'conflict' => true,
                    ];
                }
            } else {
                $rowElements[sprintf("%s (database)", $index->getName())] = [
                    'index' => $index,
                    'defined' => false,
                    'exists' => true,
                    'conflict' => false,
                ];
            }
        }

        $rows = [];
        ksort($rowElements);
        foreach ($rowElements as $name => $rowElement) {
            /**
             * @var $index Index
             */
            $index = $rowElement['index'];
            $propertiesString = [];
            foreach ($index->getProperties() as $property) {
                $propertiesString[] = $property->getName();
            }
            $propertiesString = implode($propertiesString);
            $rows[] = [
                (string) $index->getName(),
                sprintf(
                    "%s (%s)",
                    $index->getLabel(),
                    $index->getLabel() instanceof NodeLabel ? 'Node' : 'Relation'
                ),
                $index->getType()->value,
                $propertiesString,
                $rowElement['defined'] ? 'yes' : '-',
                $rowElement['exists'] ? 'yes' : '-',
                $rowElement['conflict'] ? 'yes' : '-',
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Name', 'On', 'Type', 'Properties', 'Defined in Code', 'Exists in Database', 'Conflict'])
            ->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @return Index[]
     *
     * @throws UnsupportedRelationLabelException
     * @throws UnsupportedNodeLabelException
     * @throws Neo4jSyncException
     */
    private function getNeo4jIndices(): array
    {
        $res = $this->client->runStatement(GetIndicesStatementBuilder::build()[0]);
        $indices = [];
        foreach ($res as $element) {
            /**
             * @var $element CypherMap
             */
            $label = null;
            if ('NODE' == $element->get('entityType')) {
                $label = new NodeLabel($element->get('labelsOrTypes')[0]);
            } elseif ('RELATIONSHIP' == $element->get('entityType')) {
                $label = new RelationLabel($element->get('labelsOrTypes')[0]);
            } else {
                throw new Neo4jSyncException(sprintf("Index on unsupported type %s", $element->get('labelsOrTypes')[0]));
            }
            $properties = [];
            foreach ($element->get('properties') as $property) {
                $properties[] = new Property($property);
            }
            $indices[] = new Index(
                new IndexName($element->get('name')),
                $label,
                $properties,
                IndexType::from($element->get('type'))
            );
        }

        return $indices;
    }
}
