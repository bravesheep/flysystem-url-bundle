<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface UrlResolverInterface
{
    /**
     * @return string[]
     */
    public function getSupportedAdapters();

    /**
     * Add services to the container for the given data.
     * All services should be prefixed with the given prefix.
     *
     * Returns the name of the flysystem adapter service.
     *
     * @param array $data
     * @param ContainerBuilder $container
     * @param string $prefix
     * @return string
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix);
}
