<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\Provider;

use Syndesi\Neo4jSyncBundle\Contract\NodeLabelProviderInterface;
use Syndesi\Neo4jSyncBundle\Exception\ClassNotFoundException;
use Syndesi\Neo4jSyncBundle\Exception\MissingInterfaceException;
use Syndesi\Neo4jSyncBundle\ValueObject\NodeLabel;

class InterfaceNodeLabelProvider implements NodeLabelProviderInterface
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
            if (!in_array(NodeLabelProviderInterface::class, class_implements($class))) {
                throw new MissingInterfaceException(sprintf("Class '%s' does not implement interface '%s'.", $class, NodeLabelProviderInterface::class));
            }
        }
    }

    /**
     * @throws MissingInterfaceException
     */
    public function getNodeLabel(object $entity): NodeLabel
    {
        if ($this->class) {
            /**
             * @var NodeLabelProviderInterface $instance
             */
            $instance = new $this->class();

            return $instance->getNodeLabel($entity);
        }
        if (!($entity instanceof NodeLabelProviderInterface)) {
            throw new MissingInterfaceException(sprintf("Entity of class '%s' does not implement interface '%s'.", get_class($entity), NodeLabelProviderInterface::class));
        }

        return $entity->getNodeLabel($entity);
    }
}
