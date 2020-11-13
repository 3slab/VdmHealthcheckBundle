<?php

namespace Vdm\Bundle\HealthcheckBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vdm_healthcheck');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('liveness_checker')
            ->fixXmlConfig('readiness_checker')
            ->children()
                ->scalarNode('secret')
                    ->defaultNull()
                ->end()
                ->scalarNode('liveness_path')
                    ->defaultValue('/liveness')
                ->end()
                ->scalarNode('readiness_path')
                    ->defaultValue('/readiness')
                ->end()
                ->arrayNode('liveness_checkers')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->arrayNode('arguments')
                                ->defaultValue([])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('readiness_checkers')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->arrayNode('arguments')
                                ->defaultValue([])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
