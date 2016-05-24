<?php

namespace Bravesheep\FlysystemUrlBundle\Resolver;

use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SftpUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\Sftp\SftpAdapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        if (!isset($data['host'])) {
            throw new UrlResolveException("No host found in url");
        }

        if (!isset($data['user'])) {
            throw new UrlResolveException("Username required, but not found in url");
        }

        $port = 22;
        if (isset($data['port'])) {
            if (!ctype_digit($data['port'])) {
                throw new UrlResolveException("Invalid port numer '{$data['port']}'");
            }
            $port = intval($data['port'], 10);
        }

        $password = isset($data['pass']) ? $data['pass'] : null;
        $privateKey = isset($data['keyfile']) ? $data['keyfile'] : null;
        $root = isset($data['path']) ? $data['path'] : '/';

        $timeout = 10;
        if (isset($data['timeout'])) {
            if (!ctype_digit($data['timeout'])) {
                throw new UrlResolveException(
                    "Invalid timeout '{$data['timeout']}', expected number of seconds as integer"
                );
            }
        }

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\Sftp\SftpAdapter', [[
                'host' => $data['host'],
                'port' => $port,
                'username' => $data['user'],
                'password' => $password,
                'privateKey' => $privateKey,
                'root' => $root,
                'timeout' => $timeout
            ]])
        );

        return "$prefix.adapter";
    }

}
