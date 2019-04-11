<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildDirectorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('webfactory_navigation.tree_factory.dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('webfactory_navigation.tree_factory.dispatcher');

        foreach ($container->findTaggedServiceIds('webfactory_navigation.build_director') as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 100;
                $definition->addMethodCall('addDirector', [new Reference($id), $priority]);
            }
        }
    }
}
