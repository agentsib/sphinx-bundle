<?php

namespace AgentSIB\SphinxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AgentSIBSphinxExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['connections'] as $name => $values) {
            $def = new DefinitionDecorator('agentsib_sphinx.abstract.connection');
            $def->addMethodCall('setParams', array(
                array(
                    'host'  =>  $values['host'],
                    'port'  =>  $values['port'],
                    'socket' => $values['socket']
                )
            ));
            $container->setDefinition(sprintf('agentsib_sphinx.%s.connection', $name), $def);
        }
    }

    public function getAlias()
    {
        return 'agentsib_sphinx';
    }


}
