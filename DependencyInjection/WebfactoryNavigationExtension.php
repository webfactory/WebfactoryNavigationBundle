<?php

namespace Webfactory\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Webfactory\Bundle\WfdMetaBundle\DependencyInjection\MetaQueryConfigurator;

class WebfactoryNavigationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        if (isset($config['refresh_tree_for_tables'])) {
            $config['wfd_meta_refresh']['tables'] = array_merge(
                $config['wfd_meta_refresh']['tables'],
                $config['refresh_tree_for_tables']
            );
            unset($config['refresh_tree_for_tables']);
        }

        $configurator = new MetaQueryConfigurator();
        $configurator->configure(
            $container,
            'webfactory_navigation.tree_factory.meta_query',
            $config['wfd_meta_refresh']
        );
    }
}
