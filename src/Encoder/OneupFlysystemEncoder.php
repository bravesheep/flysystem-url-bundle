<?php

namespace Bravesheep\FlysystemUrlBundle\Encoder;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class OneupFlysystemEncoder implements EncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encode(array $data, ContainerBuilder $container, $prefix)
    {
        $container->setParameter("$prefix.oneup_adapter_params", [
            'custom' => ['service' => $data['service']]
        ]);
    }
}
