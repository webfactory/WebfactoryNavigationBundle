<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->private()
        ->autowire()
        ->autoconfigure();

    $services->set(\Webfactory\Bundle\NavigationBundle\Build\BuildDispatcher::class)
        ->share(false);

    $services->alias('webfactory_navigation.tree_factory.dispatcher', \Webfactory\Bundle\NavigationBundle\Build\BuildDispatcher::class)
        ->public();

    $services->set(\Webfactory\Bundle\NavigationBundle\Build\TreeFactory::class)
        ->lazy()
        ->args([
            service('config_cache_factory'),
            '%kernel.cache_dir%/webfactory_navigation/tree.php',
            service(\Psr\Container\ContainerInterface::class),
            service('event_dispatcher'),
            service('logger')->nullOnInvalid(),
            service('debug.stopwatch')->nullOnInvalid(),
        ])
        ->tag('monolog.logger', ['channel' => 'webfactory_navigation'])
        ->tag('container.service_subscriber');

    $services->alias('webfactory_navigation.tree_factory', \Webfactory\Bundle\NavigationBundle\Build\TreeFactory::class)
        ->public();

    $services->set(\Webfactory\Bundle\NavigationBundle\Tree\Tree::class)
        ->lazy()
        ->factory([service(\Webfactory\Bundle\NavigationBundle\Build\TreeFactory::class), 'getTree']);

    $services->alias('webfactory_navigation.tree', \Webfactory\Bundle\NavigationBundle\Tree\Tree::class)
        ->public();

    $services->set(\Webfactory\Bundle\NavigationBundle\Event\ActiveNodeEventListener::class)
        ->tag('kernel.event_listener', ['event' => 'webfactory_navigation.tree_initialized', 'method' => 'initializeTree']);

    $services->alias('webfactory_navigation.event.active_node_event_listener', \Webfactory\Bundle\NavigationBundle\Event\ActiveNodeEventListener::class);

    $services->set(\Webfactory\Bundle\NavigationBundle\Twig\NavigationExtension::class)
        ->tag('twig.extension');

    $services->alias('webfactory_navigation.twig_extension', \Webfactory\Bundle\NavigationBundle\Twig\NavigationExtension::class);

    $services->set(\Webfactory\Bundle\NavigationBundle\Command\DumpTreeCommand::class)
        ->tag('console.command');

    $services->alias('webfactory_navigation.command.dump_tree_command', \Webfactory\Bundle\NavigationBundle\Command\DumpTreeCommand::class);

    $services->set(\Webfactory\Bundle\NavigationBundle\Command\LookupNodeCommand::class)
        ->tag('console.command');

    $services->alias('webfactory_navigation.command.lookup_node_command', \Webfactory\Bundle\NavigationBundle\Command\LookupNodeCommand::class);
};
