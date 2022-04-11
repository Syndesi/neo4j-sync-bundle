<?php

namespace Syndesi\Neo4jSyncBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Laudis\Neo4j\Databags\Statement;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class IndexSyncCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:index:sync';

    private EntityManagerInterface $em;
    private Neo4jClient $client;
    private Neo4jStatementHelper $neo4jStatementHelper;
    private EntityReader $entityReader;

    public function __construct(
        EntityManagerInterface $em,
        Neo4jClient $client,
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
            ->setHelp('Syncs all configured indices to the Neo4j database')
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

        $statements = [];
        foreach ($indexClasses as $indexClass) {
            foreach ((new ReflectionClass($indexClass))->getAttributes() as $attribute) {
                $nodeAttribute = $attribute->newInstance();
                if ($nodeAttribute instanceof Node) {
                    foreach ($nodeAttribute->getIndices() as $indexAttribute) {
                        $statements[] = new Statement(sprintf(
                            'DROP INDEX %s IF EXISTS',
                            $indexAttribute->getName()
                        ), []);
                        $fields = [];
                        foreach ($indexAttribute->getFields() as $field) {
                            $fields[] = sprintf('n.%s', $field);
                        }
                        $statements[] = new Statement(sprintf(
                            'CREATE %s INDEX %s FOR (n:%s) ON (%s)',
                            $indexAttribute->getType()->value,
                            $indexAttribute->getName(),
                            $nodeAttribute->getLabel(),
                            implode(', ', $fields)
                        ), []);
                    }
                }
            }
        }

        $this->client->addStatements($statements);
        $this->client->flush();

        $io->success('Finished');

        return Command::SUCCESS;
    }
}
