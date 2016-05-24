<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DropboxUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\Dropbox\DropboxAdapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        if (!isset($data['user'])) {
            throw new UrlResolveException("Missing access token (username) in url");
        }

        if (!isset($data['pass'])) {
            throw new UrlResolveException("Missing app secret (password) in url");
        }

        $accessToken = $data['user'];
        $appSecret = $data['pass'];
        $adapterPrefix = isset($data['prefix']) ? $data['prefix'] : null;

        $container->setDefinition(
            "$prefix.client",
            new Definition('Dropbox\Client', [$accessToken, $appSecret])
        );

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\Dropbox\DropboxAdapter', [
                new Reference("$prefix.client"),
                $adapterPrefix
            ])
        );

        return "$prefix.adapter";
    }

}
