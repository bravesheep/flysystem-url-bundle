<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class NullUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\Adapter\NullAdapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\Adapter\NullAdapter')
        );

        return "$prefix.adapter";
    }


}
