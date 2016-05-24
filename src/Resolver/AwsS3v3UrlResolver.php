<?php


namespace Bravesheep\FlysystemUrlBundle\Resolver;


use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AwsS3v3UrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\AwsS3v3\AwsS3Adapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        if (!isset($data['host'])) {
            throw new UrlResolveException("No region hostname found in url");
        }

        if (!isset($data['user'])) {
            throw new UrlResolveException("No key (username) found in url");
        }

        if (!isset($data['pass'])) {
            throw new UrlResolveException("No secret (password) found in url");
        }

        if (!isset($data['path'])) {
            throw new UrlResolveException("No bucket name (path) found in url");
        }

        $region = $data['host'];
        $key = $data['user'];
        $secret = $data['pass'];
        $bucket = $data['path'];

        $adapterPrefix = null;
        if (isset($data['prefix'])) {
            $adapterPrefix = $data['prefix'];
        }

        $version = 'latest';
        if (isset($data['version'])) {
            $version = $data['version'];
        }

        $client = new Definition('Aws\S3\S3Client');
        $client->setFactory(['Aws\S3\S3Client', 'factory']);
        $client->setArguments([[
            'credentials' => [
                'key' => $key,
                'secret' => $secret
            ],
            'region' => $region,
            'version' => $version
        ]]);

        $container->setDefinition("$prefix.client", $client);
        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\AwsS3v3\AwsS3Adapter', [
                new Reference("$prefix.client"),
                $bucket,
                $adapterPrefix
            ])
        );

        return "$prefix.adapter";
    }
}
