<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeRelationsProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\ClassNotFoundException;
use Syndesi\Neo4jSyncBundle\Exception\MissingInterfaceException;

class InterfaceNodeRelationsProvider implements NodeRelationsProviderInterface
{
    /**
     * @throws MissingInterfaceException
     * @throws ClassNotFoundException
     */
    public function __construct(
        private readonly ?string $class = null
    ) {
        if ($class) {
            if (!class_exists($class)) {
                throw new ClassNotFoundException(sprintf("Unable to find class with name '%s'", $class));
            }
            if (!in_array(NodeRelationsProviderInterface::class, class_implements($class))) {
                throw new MissingInterfaceException(sprintf("Class '%s' does not implement interface '%s'.", $class, NodeRelationsProviderInterface::class));
            }
        }
    }

    /**
     * @throws MissingInterfaceException
     */
    public function getNodeRelations(object $entity): array
    {
        if ($this->class) {
            /**
             * @var NodeRelationsProviderInterface $instance
             */
            $instance = new $this->class();

            return $instance->getNodeRelations($entity);
        }
        if (!($entity instanceof NodeRelationsProviderInterface)) {
            throw new MissingInterfaceException(sprintf("Entity of class '%s' does not implement interface '%s'.", get_class($entity), NodeRelationsProviderInterface::class));
        }

        return $entity->getNodeRelations($entity);
    }
}
