<?php

namespace AgentSIB\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('agentsib_sphinx');

        $rootNode
            ->beforeNormalization()
                ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('connections', $v) && array_key_exists('connection', $v); })
                ->then(function($v) {
                    $connection = $v['connection'];

                    $v['default_connection'] = isset($v['default_connection']) ? (string) $v['default_connection'] : 'default';
                    $v['connections'] = array($v['default_connection'] => $connection);

                    return $v;
                })
            ->end()
            ->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name', true)
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                    ->children()
                        ->scalarNode('driver')
                            ->defaultValue('pdo')
                            ->validate()
                                ->ifNotInArray(array('pdo', 'mysqli'))
                                ->thenInvalid('Driver not support')
                            ->end()
                        ->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->integerNode('port')
                            ->max(65535)
                            ->min(0)
                            ->defaultValue(9306)
                        ->end()
                    ->scalarNode('socket')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('default_connection')->defaultValue('default')->end()
        ;

        return $treeBuilder;
    }
}
