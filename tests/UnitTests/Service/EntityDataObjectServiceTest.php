<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Attribute\Node;
use Syndesi\Neo4jSyncBundle\Exception\UnsupportedEntityException;
use Syndesi\Neo4jSyncBundle\Object\EntityDataObject;
use Syndesi\Neo4jSyncBundle\Service\EntityDataObjectService;
use Syndesi\Neo4jSyncBundle\Tests\Assets\Entity\NotDoctrineEntity;
use Syndesi\Neo4jSyncBundle\Tests\Assets\Entity\NotNeo4jEntity;
use Syndesi\Neo4jSyncBundle\Tests\Assets\Entity\SimpleEntity;

final class EntityDataObjectServiceTest extends TestCase
{
    private EntityDataObjectService $entityDataObjectService;

    protected function setUp(): void
    {
        $this->entityDataObjectService = new EntityDataObjectService();
    }

    public function testNotDoctrineEntityException()
    {
        $notDoctrineEntity = new NotDoctrineEntity();
        $notDoctrineEntity->setId(0)
            ->setName('not Doctrine entity');

        $this->expectException(UnsupportedEntityException::class);
        $this->entityDataObjectService->getEntityDataObject($notDoctrineEntity);
    }

    public function testNotNeo4jEntityException()
    {
        $notNeo4jEntity = new NotNeo4jEntity();
        $notNeo4jEntity->setText('not Neo4j entity');

        $this->expectException(UnsupportedEntityException::class);
        $this->entityDataObjectService->getEntityDataObject($notNeo4jEntity);
    }

    public function testSimpleEntity()
    {
        $simpleEntity = new SimpleEntity();
        $simpleEntity->setId(0)
            ->setText('Neo4j & Doctrine entity');

        $entityDataObject = $this->entityDataObjectService->getEntityDataObject($simpleEntity);
        $this->assertInstanceOf(EntityDataObject::class, $entityDataObject);

        $this->assertSame(SimpleEntity::class, $entityDataObject->getEntityClass());
        $this->assertIsArray($entityDataObject->getData());
        $this->assertIsArray($entityDataObject->getProperties());
        $this->assertInstanceOf(Node::class, $entityDataObject->getNodeAttribute());

        $node = $entityDataObject->getNodeAttribute();
        $this->assertIsString($node->getId());
        $this->assertIsString($node->getLabel());
        $this->assertIsString($node->getSerializationGroup());
        $this->assertIsArray($node->getRelations());
    }
}
