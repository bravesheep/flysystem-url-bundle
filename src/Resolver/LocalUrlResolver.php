<?php


namespace Bravesheep\FlysystemUrlBundle\Resolver;


use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LocalUrlResolver implements UrlResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getSupportedAdapters()
    {
        return ['League\Flysystem\Adapter\Local'];
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

        if (!self::isAbsolute($path)) {
            throw new UrlResolveException("Configured path '$path' is not an absolute path");
        }


        $lock = LOCK_EX;
        if (isset($data['lock']) && ($data['lock'] === '0' || $data['lock'] === 'false')) {
            $lock = 0;
        }

        $filePermPublic = 0744;
        if (isset($data['file_perm'])) {
            $filePermPublic = self::parsePermissions($data['file_perm']);
        }

        $filePermPrivate = 0700;
        if (isset($data['file_perm_priv'])) {
            $filePermPrivate = self::parsePermissions($data['file_perm_priv']);
        }

        $dirPermPublic = 0755;
        if (isset($data['dir_perm'])) {
            $dirPermPublic = self::parsePermissions($data['dir_perm']);
        }

        $dirPermPrivate = 0700;
        if (isset($data['dir_perm_priv'])) {
            $dirPermPrivate = self::parsePermissions($data['dir_perm_priv']);
        }

        $container->setDefinition(
            "$prefix.adapter",
            new Definition('League\Flysystem\Adapter\Local', [
                $path,
                $lock,
                [
                    'file' => ['public' => $filePermPublic, 'private' => $filePermPrivate],
                    'dir' => ['public' => $dirPermPublic, 'private' => $dirPermPrivate]
                ]
            ])
        );

        return "$prefix.adapter";
    }

    /**
     * Checks whether the path is an absolute path.
     *
     * @param string $path
     * @return bool
     */
    private static function isAbsolute($path)
    {
        // TODO: add a better check here
        return $path[0] === '/';
    }

    /**
     * @param string $permissions
     * @return int
     * @throws UrlResolveException
     */
    private static function parsePermissions($permissions)
    {
        if (ctype_digit($permissions) && $permissons[0] === '0') {
            return octdec($permissions);
        }

        throw new UrlResolveException("Invalid file permissions specification: '$permissions'");
    }

}
