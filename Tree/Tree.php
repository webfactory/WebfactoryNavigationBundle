<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Tree;

class Tree
{
    protected $roots = array();
    protected $identities = array();
    protected $finder;

    /**
     * The currently active node in this tree.
     *
     * @var Node|null
     */
    protected $activeNode = null;

    /**
     * The "closest to active" node in this tree.
     *
     * Used whenever the "real" active node does not exist in the tree, for example
     * because it results from user input and we cannot enumerate all the possible
     * nodes when building the tree.
     *
     * @var Node|null
     */
    protected $activePath = null;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function addRoot(Node $r = null)
    {
        if ($r === null) {
            $r = new Node();
        }
        $this->roots[] = $r;
        $r->setTree($this);

        return $r;
    }

    public function getRootNodes()
    {
        return $this->roots;
    }

    public function addFindIndex(Node $n, array $requirements)
    {
        $this->finder->add($n, $requirements);
    }

    public function find(array $provisions)
    {
        return $this->finder->lookup($provisions);
    }

    /**
     * Sets the active Node.
     *
     * @param Node $n
     */
    public function setActiveNode(Node $n)
    {
        $this->activeNode = $this->activePath = $n;
    }

    /**
     * Sets a node as the "closest to active" node in the tree, but making this particular
     * node itself *not* active.
     *
     * @param Node $n
     */
    public function setActivePath(Node $n)
    {
        $this->activeNode = null;
        $this->activePath = $n;
    }

    /**
     * @return Node|null Returns the currently active node, if available.
     */
    public function getActiveNode()
    {
        return $this->activeNode;
    }

    /**
     * Returns the node that is currently active or comes closest to the actually active state. Use
     * \Webfactory\Bundle\NavigationBundle\Tree\Node::isActiveNode to query whether the node is indeed active
     * or not.
     *
     * @return Node|null
     */
    public function getActivePath()
    {
        return $this->activePath;
    }
}
