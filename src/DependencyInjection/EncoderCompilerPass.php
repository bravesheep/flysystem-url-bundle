<?php

namespace Bravesheep\FlysystemUrlBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EncoderCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bravesheep_flysystem_url.encoder_registry')) {
            return;
        }

        $definition = $container->findDefinition('bravesheep_flysystem_url.encoder_registry');

        $taggedServices = $container->findTaggedServiceIds('bravesheep_flysystem_url.encoder');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addEncoder', [
                    $attributes['alias'],
                    new Reference($id)
                ]);
            }
        }
    }
}
