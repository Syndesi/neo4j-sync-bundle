<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Syndesi\Neo4jSyncBundle\DependencyInjection\Compiler\NormalizerProviderCompilerPass;
use Syndesi\Neo4jSyncBundle\DependencyInjection\SyndesiNeo4jSyncExtension;

class SyndesiNeo4jSyncBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        $this->extension ??= new SyndesiNeo4jSyncExtension();

        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NormalizerProviderCompilerPass());
    }
}
