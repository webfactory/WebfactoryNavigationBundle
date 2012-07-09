<?php

namespace Webfactory\Bundle\NavigationBundle\Navigation;

use Webfactory\Tree\Cache\Cache;
use Webfactory\Tree\ActiveNode\ActiveNodeInterface;
use Webfactory\Navigation\Subtree;
use Webfactory\Navigation\Breadcrumbs;
use Webfactory\Navigation\NodeList;

class Factory {

    protected $tree;

    public function __construct(Cache $tree) {
        $this->tree = $tree;
    }

    public function createSubtree(ActiveNodeInterface $rootNode, $maxHeight = false, $expandedDepth = 1) {
        return new Subtree($this->tree, $rootNode, $maxHeight, $expandedDepth);
    }

    public function createNodeList() {
        return new NodeList($this->tree);
    }

    public function createBreadcrumbs(ActiveNodeInterface $rootNode) {
        return new Breadcrumbs($this->tree, $rootNode);
    }

}