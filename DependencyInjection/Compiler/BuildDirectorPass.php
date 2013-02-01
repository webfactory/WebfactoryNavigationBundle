<?php
namespace Webfactory\Bundle\NavigationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class BuildDirectorPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {

        if (false === $container->hasDefinition('webfactory_navigation.tree_factory.dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('webfactory_navigation.tree_factory.dispatcher');

        foreach ($container->findTaggedServiceIds('webfactory_navigation.build_director') as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 100;
                $definition->addMethodCall('addDirector', array(new Reference($id), $priority));
            }
        }
    }
}
