<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeIdentifierProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\ClassNotFoundException;
use Syndesi\Neo4jSyncBundle\Exception\MissingInterfaceException;
use Syndesi\Neo4jSyncBundle\ValueObject\Property;

class InterfaceNodeIdentifierProvider implements NodeIdentifierProviderInterface
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
            if (!in_array(NodeIdentifierProviderInterface::class, class_implements($class))) {
                throw new MissingInterfaceException(sprintf("Class '%s' does not implement interface '%s'.", $class, NodeIdentifierProviderInterface::class));
            }
        }
    }

    /**
     * @throws MissingInterfaceException
     */
    public function getNodeIdentifier(object $entity): Property
    {
        if ($this->class) {
            /**
             * @var NodeIdentifierProviderInterface $instance
             */
            $instance = new $this->class();

            return $instance->getNodeIdentifier($entity);
        }
        if (!($entity instanceof NodeIdentifierProviderInterface)) {
            throw new MissingInterfaceException(sprintf("Entity of class '%s' does not implement interface '%s'.", get_class($entity), NodeIdentifierProviderInterface::class));
        }

        return $entity->getNodeIdentifier($entity);
    }
}
