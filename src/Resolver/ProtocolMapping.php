<?php


namespace Bravesheep\FlysystemUrlBundle\Resolver;

class ProtocolMapping
{
    /**
     * @var array[]
     */
    private static $mappings = [
        'local' => ['adapter' => 'League\Flysystem\Adapter\Local'],
        'file' => ['adapter' => 'League\Flysystem\Adapter\Local'],
        'null' => ['adapter' => 'League\Flysystem\Adapter\NullAdapter'],
        'zip' => ['adapter' => 'League\Flysystem\ZipArchive\ZipArchiveAdapter'],
        'awss3v2' => ['adapter' => 'League\Flysystem\AwsS3v2\AwsS3Adapter'],
        'awss3v3' => ['adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter'],
        'awss3' => ['adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter'],
        's3' => ['adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter'],
        'dropbox' => ['adapter' => 'League\Flysystem\Dropbox\DropboxAdapter'],
        'webdav' => ['adapter' => 'League\Flysystem\WebDAV\WebDAVAdapter'],
        'http' => ['adapter' => 'League\Flysystem\WebDAV\WebDAVAdapter', 'ssl' => false],
        'https' => ['adapter' => 'League\Flysystem\WebDAV\WebDAVAdapter', 'ssl' => true],
        'ftp' => ['adapter' => 'League\Flysystem\Adapter\Ftp', 'ssl' => false],
        'ftps' => ['adapter' => 'League\Flysystem\Adapter\Ftp', 'ssl' => true],
        'sftp' => ['adapter' => 'League\Flysystem\Sftp\SftpAdapter'],
        'ssh' => ['adapter' => 'League\Flysystem\Sftp\SftpAdapter']
    ];
    
    private static $tryAsFile = ['file', 'local', 'zip'];

    /**
     * @param string $protocol
     * @return bool|string
     */
    public static function getAdapterClass($protocol)
    {
        if (isset(self::$mappings[$protocol])) {
            return self::$mappings[$protocol]['adapter'];
        }

        return false;
    }

    /**
     * @param string $protocol
     * @return bool|array
     */
    public static function getAdapter($protocol)
    {
        if (isset(self::$mappings[$protocol])) {
            return self::$mappings[$protocol];
        }

        return false;
    }

    /**
     * @param string $protocol
     * @return bool
     */
    public static function isValidProtocol($protocol)
    {
        return isset(self::$mappings[$protocol]);
    }

    public static function shouldTryAsFile($protocol)
    {
        return in_array($protocol, self::$tryAsFile, true);
    }
}
