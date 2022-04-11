<?php

namespace Syndesi\Neo4jSyncBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syndesi\Neo4jSyncBundle\Enum\CreateType;
use Syndesi\Neo4jSyncBundle\Service\EntityReader;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClient;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementHelper;

class DatabaseSyncCommand extends Command
{
    protected static $defaultName = 'neo4j-sync:db:sync';

    private EntityManagerInterface $em;
    private Neo4jClient $client;
    private Neo4jStatementHelper $neo4jStatementHelper;
    private EntityReader $entityReader;
    private int $pageSize;

    public function __construct(
        EntityManagerInterface $em,
        Neo4jClient $client,
        Neo4jStatementHelper $neo4jStatementHelper,
        EntityReader $entityReader,
        int $pageSize
    ) {
        $this->em = $em;
        $this->client = $client;
        $this->neo4jStatementHelper = $neo4jStatementHelper;
        $this->entityReader = $entityReader;
        $this->pageSize = $pageSize;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Syncs all data from database to Neo4j')
            ->addOption(
                'create',
                null,
                InputOption::VALUE_NEGATABLE,
                'If used nodes will be created with CREATE, not MERGE. Is faster overall, but will create duplicate nodes if Neo4j database is not empty.',
                false
            )
        ;
    }

    /**
     * @throws
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nodeClasses = [];
        $relationClasses = [];
        // iterate over all Doctrine entities and check if they are Neo4j nodes
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $meta) {
            $className = $meta->getName();
            if ($this->entityReader->isEntityClassSupported($className)) {
                $nodeClasses[] = $className;
                if ($this->entityReader->hasEntityClassRelations($className)) {
                    $relationClasses[] = $className;
                }
            }
        }

        $io->section('Synchronizing nodes');

        // create nodes
        foreach ($nodeClasses as $class) {
            $entityRepository = $this->em->getRepository($class);
            $count = $entityRepository->count([]);
            if (0 === $count) {
                $io->writeln(sprintf('No elements of type %s found.', $class));
                $io->newLine();
                continue;
            }
            $io->writeln(sprintf('Found %d elements of type %s to be synchronized.', $count, $class));
            $i = 0;
            while ($i * $this->pageSize <= $count) {
                $io->writeln(sprintf(
                    'Synchronizing elements %d to %d of %d... ',
                    $i * $this->pageSize + 1,
                    min(($i + 1) * $this->pageSize, $count),
                    $count
                ));
                $entities = $entityRepository
                    ->createQueryBuilder('n')
                    ->setFirstResult($i * $this->pageSize)
                    ->setMaxResults($this->pageSize)
                    ->getQuery()
                    ->getResult();
                if ($input->getOption('create')) {
                    $this->client->addStatements($this->neo4jStatementHelper->getNodeStatementsForEntityList($entities, CreateType::CREATE));
                } else {
                    $this->client->addStatements($this->neo4jStatementHelper->getNodeStatementsForEntityList($entities, CreateType::MERGE));
                }
                $this->client->flush();
                ++$i;
            }
            $io->newLine();
        }

        $io->section('Synchronizing relations');

        // create relations
        foreach ($relationClasses as $class) {
            $entityRepository = $this->em->getRepository($class);
            $count = $entityRepository->count([]);
            if (0 === $count) {
                $io->writeln(sprintf('No elements of type %s found.', $class));
                $io->newLine();
                continue;
            }
            $io->writeln(sprintf('Found %d elements with relations of type %s to be synchronized.', $count, $class));
            $i = 0;
            while ($i * $this->pageSize <= $count) {
                $io->writeln(sprintf(
                    'Synchronizing relations of elements %d to %d of %d... ',
                    $i * $this->pageSize + 1,
                    min(($i + 1) * $this->pageSize, $count),
                    $count
                ));
                $entities = $entityRepository
                    ->createQueryBuilder('n')
                    ->setFirstResult($i * $this->pageSize)
                    ->setMaxResults($this->pageSize)
                    ->getQuery()
                    ->getResult();
                if ($input->getOption('create')) {
                    $this->client->addStatements($this->neo4jStatementHelper->getRelationStatementsForEntityList($entities, CreateType::CREATE));
                } else {
                    $this->client->addStatements($this->neo4jStatementHelper->getRelationStatementsForEntityList($entities, CreateType::MERGE));
                }
                $this->client->flush();
                ++$i;
            }
            $io->newLine();
        }

        $io->success('Finished');

        return Command::SUCCESS;
    }
}
