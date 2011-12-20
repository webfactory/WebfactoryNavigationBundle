<?php

namespace Webfactory\Bundle\NavigationBundle\Navigation;

use Webfactory\Tree\Cache\Cache;
use Webfactory\Tree\ActiveNode\ActiveNodeInterface;
use Webfactory\Navigation\Subtree;

class Factory {

    protected $tree;

    public function __construct(Cache $tree) {
        $this->tree = $tree;
    }

    public function createSubtree(ActiveNodeInterface $rootNode, $maxHeight = false, $expandedDepth = 1) {
        return new Subtree($this->tree, $rootNode, $maxHeight, $expandedDepth);
    }

}