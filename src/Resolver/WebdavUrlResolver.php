<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class WebdavUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\WebDAV\WebDAVAdapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        if (!isset($data['host'])) {
            throw new UrlResolveException("No host found in url");
        }

        $ssl = isset($data['ssl']) ? (bool)$data['ssl'] : false;
        $port = $ssl ? 443 : 80;
        if (isset($data['port'])) {
            if (!ctype_digit($data['port'])) {
                throw new UrlResolveException("Invalid port numer '{$data['port']}'");
            }
            $port = intval($data['port'], 10);
        }

        $host = $data['host'];
        $protocol = $ssl ? 'https' : 'http';
        $path = isset($data['path']) ? $data['path'] : '';

        $settings = [
            'baseUri' => "$protocol://$host:$port/$path"
        ];

        if (isset($data['user'])) {
            $settings['userName'] = $data['user'];
        }

        if (isset($data['pass'])) {
            $settings['password'] = $data['pass'];
        }

        if (isset($data['proxy'])) {
            $settings['proxy'] = $data['proxy'];
        }

        $adapterPrefix = null;
        if (isset($data['prefix'])) {
            $adapterPrefix = $data['prefix'];
        }

        $container->setDefinition(
            "$prefix.client",
            new Definition('Sabre\DAV\Client', [$settings])
        );

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\WebDAV\WebDAVAdapter', [
                new Reference("$prefix.client"),
                $adapterPrefix
            ])
        );

        return "$prefix.adapter";
    }

}
