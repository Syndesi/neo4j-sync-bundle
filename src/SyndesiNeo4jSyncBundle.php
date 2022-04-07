<?php

namespace Syndesi\Neo4jSyncBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Syndesi\Neo4jSyncBundle\DependencyInjection\SyndesiNeo4jSyncExtension;

class SyndesiNeo4jSyncBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new SyndesiNeo4jSyncExtension();
        }

        return $this->extension;
    }
}
