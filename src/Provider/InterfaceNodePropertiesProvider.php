<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodePropertiesProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\ClassNotFoundException;
use Syndesi\Neo4jSyncBundle\Exception\MissingInterfaceException;

class InterfaceNodePropertiesProvider implements NodePropertiesProviderInterface
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
            if (!in_array(NodePropertiesProviderInterface::class, class_implements($class))) {
                throw new MissingInterfaceException(sprintf("Class '%s' does not implement interface '%s'.", $class, NodePropertiesProviderInterface::class));
            }
        }
    }

    /**
     * @throws MissingInterfaceException
     */
    public function getNodeProperties(object $entity): array
    {
        if ($this->class) {
            /**
             * @var NodePropertiesProviderInterface $instance
             */
            $instance = new $this->class();

            return $instance->getNodeProperties($entity);
        }
        if (!($entity instanceof NodePropertiesProviderInterface)) {
            throw new MissingInterfaceException(sprintf("Entity of class '%s' does not implement interface '%s'.", get_class($entity), NodePropertiesProviderInterface::class));
        }

        return $entity->getNodeProperties($entity);
    }
}
