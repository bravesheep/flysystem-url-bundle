<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FtpUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\Adapter\Ftp'];
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

        $passive = true;
        if (isset($data['passive']) && ($data['passive'] === '0' || $data['passive'] === 'false')) {
            $passive = false;
        }

        $port = $ssl ? 990 : 21;
        if (isset($data['port'])) {
            if (!ctype_digit($data['port'])) {
                throw new UrlResolveException("Invalid port numer '{$data['port']}'");
            }
            $port = intval($data['port'], 10);
        }

        $timeout = 30;
        if (isset($data['timeout'])) {
            if (!ctype_digit($data['timeout'])) {
                throw new UrlResolveException(
                    "Invalid timeout '{$data['timeout']}', expected number of seconds as integer"
                );
            }
        }

        $root = '/';
        if (isset($data['path'])) {
            $root = $data['path'];
        }

        $username = isset($data['user']) ? $data['user'] : 'anonymous';
        $password = isset($data['pass']) ? $data['pass'] : '';

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\Adapter\Ftp', [[
                'host' => $data['host'],
                'username' => $username,
                'password' => $password,
                'port' => $port,
                'root' => $root,
                'passive' => $passive,
                'ssl' => $ssl,
                'timeout' => $timeout
            ]])
        );

        return "$prefix.adapter";
    }


}
