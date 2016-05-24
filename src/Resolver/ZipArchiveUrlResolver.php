<?php


namespace Bravesheep\FlysystemUrlBundle\Resolver;


use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ZipArchiveUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\ZipArchive\ZipArchiveAdapter'];
    }

    /**
     * @inheritDoc
     */
    public function createServiceDefinitions(array $data, ContainerBuilder $container, $prefix)
    {
        if (isset($data['host']) && isset($data['path'])) {
            $path = "{$data['host']}/{$data['path']}";
        } else if (isset($data['path'])) {
            $path = $data['path'];
        } else {
            throw new UrlResolveException('No path to storage location found');
        }

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\ZipArchive\ZipArchiveAdapter', [
                $path,
            ])
        );

        return "$prefix.adapter";
    }
}
