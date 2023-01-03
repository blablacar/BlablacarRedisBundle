<?php

namespace Blablacar\RedisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('blablacar_redis');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('clients')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->defaultValue('127.0.0.1')->cannotBeEmpty()->end()
                            ->integerNode('port')->defaultValue(6379)->end()
                            ->floatNode('timeout')
                                ->min(0.0)
                                ->defaultValue(0.0)
                            ->end()
                            ->integerNode('base')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('session')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('client')->isRequired()->end()
                        ->scalarNode('prefix')->defaultValue('session')->cannotBeEmpty()->end()
                        ->integerNode('ttl')->end()
                        ->integerNode('spin_lock_wait')->defaultValue(150000)->end()
                        ->integerNode('lock_max_wait')->defaultValue(500000)->end()
                    ->end()
                ->end()
                ->booleanNode('enable_logger')->defaultTrue()->end()
                ->booleanNode('public')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
