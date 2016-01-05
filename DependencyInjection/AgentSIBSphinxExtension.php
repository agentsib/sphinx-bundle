<?php

namespace AgentSIB\SphinxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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
            $def = new Definition();
            $def->setClass($container->getParameter('agentsib_sphinx.connection.class'));

            $params = array(
                'host'  =>  $values['host'],
                'port'  =>  $values['port'],
            );

            if ($values['socket']) {
                $params['socket'] = $values['socket'];
            }

            $def->setArguments(array(
                $name,
                $values['driver'],
                $params
            ));

            $def->addMethodCall('addLogger', array(new Reference('agentsib_sphinx.logger')));

            $container->setDefinition(sprintf('agentsib_sphinx.%s.connection', $name), $def);

            $helperDef = new DefinitionDecorator('agentsib_sphinx.factory.helper');
            $helperDef->setFactoryClass($container->getParameter('agentsib_sphinx.helper.class'));
            $helperDef->setFactoryMethod('create');
            $helperDef->addArgument(new Reference(sprintf('agentsib_sphinx.%s.connection', $name)));
            $helperDef->setPublic(true);
            $container->setDefinition(sprintf('agentsib_sphinx.%s.helper', $name), $helperDef);



            if ($name == $config['default_connection']) {
                $container->setAlias('agentsib_sphinx.connection', sprintf('agentsib_sphinx.%s.connection', $name));
                $container->setAlias('agentsib_sphinx.helper', sprintf('agentsib_sphinx.%s.helper', $name));
            }

        }
    }

    public function getAlias()
    {
        return 'agentsib_sphinx';
    }


}
