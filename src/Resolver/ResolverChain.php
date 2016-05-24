<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolverChain implements UrlResolverInterface
{
    /**
     * @var UrlResolverInterface[]
     */
    private $resolvers = [];

    /**
     * @param UrlResolverInterface $resolver
     */
    public function addResolver(UrlResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        $supported = [];
        foreach ($this->resolvers as $resolver) {
            $supported = array_merge($supported, $resolver->getSupportedAdapters());
        }

        return $supported;
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        $service = null;
        foreach ($this->resolvers as $resolver) {
            if (in_array($data['adapter'], $resolver->getSupportedAdapters(), true)) {
                $service = $resolver->createServiceDefinitions($data, $container, $prefix);
                break;
            }
        }

        if (null === $service) {
            throw new UrlResolveException("No resolver found for url '{$data['original_url']}'");
        }

        return $service;
    }


}
