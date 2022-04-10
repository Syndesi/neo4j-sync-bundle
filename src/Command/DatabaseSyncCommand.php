<?php

namespace Syndesi\Neo4jSyncBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    public const PAGE_SIZE = 100;

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
            ->setHelp('Syncs all data from database to Neo4j')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $classes = [];
        $metas = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($metas as $meta) {
            $className = $meta->getName();
            if ($this->entityReader->isEntityClassSupported($className)) {
                $classes[] = $className;
            }
        }
        var_dump($classes);

        foreach ($classes as $class) {
            $entityRepository = $this->em->getRepository($class);
            $count = $entityRepository->count([]);
            $output->writeln(sprintf('Found %d elements of type %s to be synchronized.', $count, $class));
            $i = 0;
            while ($i * self::PAGE_SIZE <= $count) {
                $output->writeln(sprintf('Synchronizing elements %d to %d... ', $i * self::PAGE_SIZE, ($i + 1) * self::PAGE_SIZE - 1));
                $entities = $entityRepository
                    ->createQueryBuilder('n')
                    ->setFirstResult($i * self::PAGE_SIZE)
                    ->setMaxResults(self::PAGE_SIZE)
                    ->getQuery()
                    ->getResult();
//                foreach ($entities as $entity) {
//                    $this->client->addStatements($this->neo4jStatementHelper->getCreateStatements($entity));
//                }
                $this->client->addStatements($this->neo4jStatementHelper->getNodeCreateStatementsForEntityList($entities));
                $output->writeln('statement preparation finished');
                $this->client->flush();
                ++$i;
            }
        }

        return Command::SUCCESS;
    }
}
