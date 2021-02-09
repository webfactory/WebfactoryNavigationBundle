<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webfactory\Bundle\NavigationBundle\Build\TreeFactory;
use Webfactory\Bundle\NavigationBundle\Tree\Node;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class NavigationExtension extends AbstractExtension
{
    /** @var TreeFactory */
    private $treeFactory;

    public function __construct(TreeFactory $treeFactory)
    {
        $this->treeFactory = $treeFactory;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_active_at_level', [$this, 'getNavigationActiveAtLevel']),
            new TwigFunction('navigation_find', [$this, 'findNode']),
            new TwigFunction('navigation_active_node', [$this, 'getActiveNode']),
            new TwigFunction('navigation_active_path', [$this, 'getActivePath']),
            new TwigFunction('additional_navigation_item_classes', [$this, 'getAdditionalNavigationItemClasses']),
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

    public function getAdditionalNavigationItemClasses(Node $node, array $loop, int $level): string
    {
        $baseClasses = [
            $node->isActiveNode() ? 'a' : 'na',
            $node->isActivePath() ? 'ap' : 'nap',
            $loop['first'] ? 'f' : 'nf',
            $loop['last'] ? 'l' : 'nl',
            $node->hasVisibleChildren() ? 'p' : 'np',
        ];

        $additionalNavigationItemClasses = [];
        foreach ($this->getPowerSet($baseClasses) as $set) {
            sort($set);
            $classQualifier = implode('-', $set);
            if ($classQualifier) {
                $additionalNavigationItemClasses[] = 'ni-'.$classQualifier.'-'.$level;
            }
        }

        return implode(' ', $additionalNavigationItemClasses);
    }

    private function getPowerSet(array $baseSet)
    {
        $count = \count($baseSet);
        $members = pow(2, $count);
        $powerSet = [];
        for ($i = 0; $i < $members; ++$i) {
            $b = sprintf('%0'.$count.'b', $i);
            $out = [];
            for ($j = 0; $j < $count; ++$j) {
                if ('1' == $b[$j]) {
                    $out[] = $baseSet[$j];
                }
            }
            $powerSet[] = $out;
        }

        return $powerSet;
    }
}
