<?php

namespace Webfactory\Bundle\NavigationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UrlGeneratorPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $definition = $container->getDefinition('webfactory.navigation.twig_extension');

        foreach ($container->findTaggedServiceIds('webfactory.navigation.url_generator') as $id => $tags) {
            $definition->addMethodCall('addUrlGenerator', array(new Reference($id)));
        }
    }

}