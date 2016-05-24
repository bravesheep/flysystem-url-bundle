<?php

namespace Bravesheep\FlysystemUrlBundle\DependencyInjection;

use Bravesheep\FlysystemUrlBundle\Encoder\EncoderRegistry;
use Bravesheep\FlysystemUrlBundle\Exception\EncodeException;
use Bravesheep\FlysystemUrlBundle\Exception\UrlResolveException;
use Bravesheep\FlysystemUrlBundle\Resolver\Decoder;
use Bravesheep\FlysystemUrlBundle\Resolver\ResolverChain;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BravesheepFlysystemUrlExtension extends Extension
{
    /**
     * @var ResolverChain
     */
    private $resolver;

    /**
     * @var EncoderRegistry
     */
    private $encoderRegistry;

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['encoders'] as &$entry) {
            foreach ($entry as &$value) {
                $value = $container->getParameterBag()->resolveValue($value);
            }
        }

        $this->loadResolverChain();
        $this->loadEncoderRegistry($config['encoders']);

        $this->processUrlConfigs($config['urls'], $container);
    }

    private function processUrlConfigs(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $data = Decoder::decode($config['url']);
            $service = $this->resolver->createServiceDefinitions($data, $container, $config['prefix']);
            if (!is_string($service) || !$container->hasDefinition($service)) {
                throw new UrlResolveException("No service name returned or a non-existant service was returned");
            }

            $data['service'] = $service;

            foreach ($config['encoders'] as $encoder) {
                if (!$this->encoderRegistry->hasEncoder($encoder)) {
                    throw new EncodeException("No encoder with the name '$encoder' found");
                }

                $this->encoderRegistry->getEncoder($encoder)->encode($data, $container, $config['prefix']);
            }
        }
    }

    private function loadResolverChain()
    {
        if (!isset($this->resolver)) {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('resolvers.yml');

            $pass = new UrlResolverCompilerPass();
            $pass->process($container);

            $this->resolver = $container->get('bravesheep_flysystem_url.resolver');
        }
    }

    /**
     * @param array $config
     */
    private function loadEncoderRegistry(array $config = [])
    {
        if (!isset($this->encoderRegistry)) {
            $container = new ContainerBuilder();
            foreach ($config as $name => $subconfig) {
                foreach ($subconfig as $param => $value) {
                    $container->setParameter("$name.$param", $value);
                }
            }

            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('encoders.yml');

            $pass = new EncoderCompilerPass();
            $pass->process($container);

            $this->encoderRegistry = $container->get('bravesheep_flysystem_url.encoder_registry');
        }
    }
}
