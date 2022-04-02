<?php
namespace App\Command;

use App\Entity\Book;
use App\Entity\Demo;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Laudis\Neo4j\Contracts\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syndesi\Neo4jSyncBundle\Service\Neo4jStatementService;
use Syndesi\Neo4jSyncBundle\Service\Neo4jClientService;

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    private EntityManagerInterface $em;
    private Neo4jStatementService $neo4jStatementService;
    private ClientInterface $client;

    public function __construct(
        EntityManagerInterface $em,
        Neo4jStatementService $neo4jStatementService,
        Neo4jClientService $neo4jClientService
    )
    {
        $this->em = $em;
        $this->neo4jStatementService = $neo4jStatementService;
        $this->client = $neo4jClientService->getClient();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$book = new Book();
        //$book->setDescription('desc')
        //    ->setIsbn('ISBN 12213123')
        //    ->setTitle('title')
        //    ->setYear('2022');

        $demo = new Demo();
        $demo->setInt(123)
            ->setFloat(12.3)
            ->setBool(true)
            ->setText('some text')
            ->setDatetime(new DateTime());
        $this->em->persist($demo);
        $this->em->flush();

        //echo($demo->getId()->toString()."\n");
        //exit;


        //$isSupported = $this->entityNormalizerService->isEntitySupported($book);
        //if ($isSupported) {
        //    echo('is supported'."\n");
        //} else {
        //    echo('is NOT supported'."\n");
        //}

        $statement = $this->neo4jStatementService->getCreateStatement($demo);
        $this->client->runStatement($statement);

        return Command::SUCCESS;
    }
}
