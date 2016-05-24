<?php

namespace Bravesheep\FlysystemUrlBundle\Encoder;

use Bravesheep\FlysystemUrlBundle\Exception\EncodeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\VarDumper\VarDumper;

class PublicUrlPrefixEncoder implements EncoderInterface
{
    /**
     * @var mixed
     */
    private $default;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @param string $webDir
     * @param mixed $default
     */
    public function __construct($webDir, $default = null)
    {
        $this->default = $default;
        $this->webDir = self::normalizePath($webDir);
    }

    /**
     * @inheritDoc
     */
    public function encode(array $data, ContainerBuilder $container, $prefix)
    {
        $value = $this->default;

        switch ($data['adapter']) {
            case 'League\Flysystem\Adapter\Local':
                $value = $this->getLocalParam($data['path']);
                break;
            case 'League\Flysystem\AwsS3v2\AwsS3Adapter':
            case 'League\Flysystem\AwsS3v3\AwsS3Adapter':
                $value = $this->getAwsParam($data);
                break;
        }

        $container->setParameter("$prefix.public_url_prefix", $value);

    }

    /**
     * @param string $path
     * @return mixed
     */
    private function getLocalParam($path)
    {
        $path = self::normalizePath($path);

        if (strpos($path, $this->webDir) === 0) {
            $url = substr($path, strlen($this->webDir));
            if (strlen($url) === 0 || $url[0] !== '/') {
                $url = '/' . $url;
            }

            return $url;
        } else {
            return $this->default;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function getAwsParam(array $data)
    {
        return "https://s3-{$data['host']}.amazonaws.com/{$data['path']}";
    }

    /**
     * @param string $path
     * @return string
     * @throws EncodeException
     */
    private static function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);

        $segments = explode('/', $path);
        $results = [];


        foreach ($segments as $segment) {
            if ($segment === '.') {
                continue;
            } else if ($segment === '..') {
                if (count($results) === 0) {
                    throw new EncodeException("Malformed path going outside filesystem root");
                }

                array_pop($results);
            } else {
                $results[] = $segment;
            }
        }

        return implode('/', $results);
    }
}
