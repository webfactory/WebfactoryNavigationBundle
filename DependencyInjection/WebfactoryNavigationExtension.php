<?php

namespace Webfactory\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class WebfactoryNavigationExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $definition = $container->getDefinition('webfactory_navigation.tree_factory');

        foreach ($configs as $subConfig) {
            if (isset($subConfig['refresh_tree_for_tables'])) {
                $definition->addMethodCall('addTableDependency', array($subConfig['refresh_tree_for_tables']));
            }
        }

    }

}
