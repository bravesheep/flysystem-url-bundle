<?php

namespace Bravesheep\FlysystemUrlBundle\Encoder;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface EncoderInterface
{
    /**
     * @param array $data
     * @param ContainerBuilder $container
     * @param string $prefix
     * @return mixed
     */
    public function encode(array $data, ContainerBuilder $container, $prefix);
}
