<?php

namespace Webfactory\Bundle\NavigationBundle\Tree;

class Tree {
    protected $roots = array();
    protected $identities = array();
    protected $finder;
    protected $activeNode = null, $activePath = null;

    public function __construct() {
        $this->finder = new Finder();
    }

    public function addRoot(Node $r = null) {
        if ($r === null)
            $r = new Node();
        $this->roots[] = $r;
        $r->setTree($this);

        return $r;
    }

    public function getRootNodes() {
        return $this->roots;
    }

    public function addFindIndex(Node $n, array $requirements) {
        $this->finder->add($n, $requirements);
    }

    public function find(array $provisions) {
        return $this->finder->lookup($provisions);
    }

    public function setActiveNode(Node $n) {
        $this->activeNode = $this->activePath = $n;
    }

    public function setActivePath(Node $n) {
        $this->activeNode = null;
        $this->activePath = $n;
    }

    public function getActiveNode() {
        return $this->activeNode;
    }

    public function getActivePath() {
        return $this->activePath;
    }
}
