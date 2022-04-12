<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Types\CypherMap;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Contract\Neo4jClientInterface;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class IndexListCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:index:list';

    private EntityManagerInterface $em;
    private Neo4jClientInterface $client;
    private Neo4jStatementHelper $neo4jStatementHelper;
    private EntityReader $entityReader;

    public function __construct(
        EntityManagerInterface $em,
        Neo4jClientInterface $client,
        Neo4jStatementHelper $neo4jStatementHelper,
        EntityReader $entityReader
    ) {
        $this->em = $em;
        $this->client = $client;
        $this->neo4jStatementHelper = $neo4jStatementHelper;
        $this->entityReader = $entityReader;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Displays all configured indices and if they are already synced to the Neo4j database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $indexClasses = [];
        // iterate over all Doctrine entities and check if they are Neo4j nodes & contain indices
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            $className = $meta->getName();
            if ($this->entityReader->isEntityClassSupported($className)) {
                if ($this->entityReader->hasEntityClassIndices($className)) {
                    $indexClasses[] = $className;
                }
            }
        }

        // add indices from schema
        $indexRows = [];
        foreach ($indexClasses as $indexClass) {
            foreach ((new ReflectionClass($indexClass))->getAttributes() as $attribute) {
                $nodeAttribute = $attribute->newInstance();
                if ($nodeAttribute instanceof Node) {
                    foreach ($nodeAttribute->getIndices() as $indexAttribute) {
                        $row = [
                            $indexClass,
                            $nodeAttribute->getLabel(),
                            $indexAttribute->getName(),
                            $indexAttribute->getType()->value,
                            implode(', ', $indexAttribute->getFields()),
                            $this->isIndexSynchronized($indexAttribute->getName()) ? 'true' : 'false',
                        ];
                        $indexRows[] = $row;
                    }
                }
            }
        }

        $neo4jIndices = $this->getAllNeo4jIndices();

        foreach ($neo4jIndices as $index) {
            foreach ($indexRows as $row) {
                if ($index[2] === $row[2]) {
                    // if label name is already in table, then skip current Neo4j index
                    continue 2;
                }
            }
            $indexRows[] = $index;
        }

        if (count($indexRows) > 0) {
            (new Table($output))
                ->setHeaders([
                    'Class',
                    'Node Label',
                    'Index Name',
                    'Index Type',
                    'Fields',
                    'Synced to Neo4j Database',
                ])
                ->setRows($indexRows)
                ->render();
        } else {
            $io->writeln('No indices found');
        }

        return Command::SUCCESS;
    }

    private function isIndexSynchronized(string $name): bool
    {
        $result = $this->client->getClient()->runStatement(new Statement(
            'SHOW INDEXES WHERE name = $name',
            [
                'name' => $name,
            ]
        ));

        return $result->count() > 0;
    }

    private function getAllNeo4jIndices(): array
    {
        $result = $this->client->getClient()->runStatement(new Statement(
            'SHOW INDEXES WHERE entityType = \'NODE\'', []
        ));
        /**
         * @var $elements CypherMap[]
         */
        $elements = $result->toArray();
        $rows = [];
        foreach ($elements as $element) {
            $rows[] = [
                '- unmanaged -',
                implode(', ', $element->get('labelsOrTypes')->toArray()),
                $element->get('name'),
                $element->get('type'),
                implode(', ', $element->get('properties')->toArray()),
                'true',
            ];
        }

        return $rows;
    }
}
