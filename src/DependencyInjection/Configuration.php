<?php


namespace Bravesheep\FlysystemUrlBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bravesheep_flysystem_url');

        $rootNode
            ->children()
                ->arrayNode('encoders')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('public_url_prefix')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default')->defaultNull()->end()
                                ->scalarNode('web_dir')->defaultValue('%kernel.root_dir%/../web')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('urls')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('url')->isRequired()->end()
                            ->scalarNode('prefix')->isRequired()->end()
                            ->arrayNode('encoders')
                                ->prototype('scalar')->end()
                                ->defaultValue(['oneup_flysystem', 'public_url_prefix'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
