<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Webfactory\Bundle\NavigationBundle\Build\TreeFactory;
use Webfactory\Bundle\NavigationBundle\Tree\Node;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class NavigationExtension extends \Twig_Extension
{
    /** @var TreeFactory */
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
            new \Twig_SimpleFunction('navigation_find', [$this, 'findNode']),
            new \Twig_SimpleFunction('navigation_active_node', [$this, 'getActiveNode']),
            new \Twig_SimpleFunction('navigation_active_path', [$this, 'getActivePath']),
        ];
    }

    /**
     * Returns the currently active tree node.
     *
     * @return Node
     */
    public function getActiveNode()
    {
        return $this->getTree()->getActiveNode();
    }

    /**
     * Returns the currently active "path" node.
     *
     * @return Node
     */
    public function getActivePath()
    {
        return $this->getTree()->getActivePath();
    }

    /**
     * Returns the navigation node which lies on the currently active path at the given level.
     *
     * @param $level The level of the node to be returned
     *
     * @return \Webfactory\Bundle\NavigationBundle\Tree\Node|null A node or null if no node at the given level exists
     */
    public function getNavigationActiveAtLevel($level)
    {
        $activeNode = $this->getTree()->getActiveNode();

        if (!$activeNode) {
            return null;
        }

        $path = $activeNode->getPath();

        if (isset($path[$level])) {
            return $path[$level];
        }

        return null;
    }

    /**
     * Finds a node indexed in the tree. See \Webfactory\Bundle\NavigationBundle\Tree\Tree::find.
     *
     * @param array $provisions parameters used to look up the node
     *
     * @return \Webfactory\Bundle\NavigationBundle\Tree\Node|null
     */
    public function findNode(array $provisions)
    {
        return $this->getTree()->find($provisions);
    }

    /**
     * @return Tree
     */
    private function getTree()
    {
        return $this->treeFactory->getTree();
    }
}
