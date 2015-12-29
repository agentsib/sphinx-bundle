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
                ->ifTrue()
                ->then(function ($v) { return isset($v[0])?array('default' => $v[0]):$v; })
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
                        ->scalarNode('host')->end()
                        ->integerNode('port')
                            ->max(65000)
                            ->min(0)
                            ->defaultNull()
                        ->end()
                    ->scalarNode('socket')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('default_connection')->end()
        ;

        return $treeBuilder;
    }
}
