<?php

namespace Bravesheep\FlysystemUrlBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UrlResolverCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bravesheep_flysystem_url.resolver')) {
            return;
        }

        $definition = $container->findDefinition('bravesheep_flysystem_url.resolver');

        $taggedServices = $container->findTaggedServiceIds('bravesheep_flysystem_url.resolver');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addResolver', [new Reference($id)]);
        }
    }
}
