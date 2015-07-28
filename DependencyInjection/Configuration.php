<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('webfactory_navigation');

        $rootNode
            ->children()

            ->arrayNode('refresh_tree_for_tables')
                ->info("Deprecated - list of table names or IDs that must re-generate the tree when changed.")
                ->prototype('scalar')->end()
            ->end()

            ->arrayNode('wfd_meta_refresh')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('tables')
                        ->info("List of table names or IDs the tree depends on")
                        ->prototype('scalar')->end()
                        ->defaultValue(array())
                    ->end()
                    ->arrayNode('entities')
                        ->info("List of Doctrine entity classes the tree depends on")
                        ->prototype('scalar')->end()
                        ->defaultValue(array())
                    ->end()
                ->end()
            ->end()

        ->end();

        return $treeBuilder;
    }
}
