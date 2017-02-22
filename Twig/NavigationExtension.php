<?php
namespace Webfactory\Bundle\NavigationBundle\Twig;

use Webfactory\Bundle\NavigationBundle\Build\TreeFactory;

class NavigationExtension extends \Twig_Extension
{
    /** @var  TreeFactory */
    private $treeFactory;

    public function __construct(TreeFactory $treeFactory)
    {
        $this->treeFactory = $treeFactory;
    }

    public function getName()
    {
        return 'webfactory_navigation_extension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('navigation_active_at_level', [$this, 'getNavigationActiveAtLevel']),
        ];
    }

    /**
     * Returns the navigation node which lies on the currently active path at the given level.
     *
     * @param $level The level of the node to be returned
     *
     * @return null|\Webfactory\Bundle\NavigationBundle\Tree\Node A node or null if no node at the given level exists
     */
    public function getNavigationActiveAtLevel($level)
    {
        $tree = $this->treeFactory->getTree();
        $path = $tree->getActiveNode()->getPath();

        if (isset($path[$level])) {
            return $path[$level];
        }

        return null;
    }
}
