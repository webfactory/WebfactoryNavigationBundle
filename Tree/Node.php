<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Tree;

class Node implements \ArrayAccess
{
    /**
     * @var Node[]
     */
    protected $children = [];

    /**
     * @var Node|null
     */
    protected $parent = null;

    protected $data = ['visible' => false, 'breadcrumbsVisible' => true, 'url' => false, 'caption' => ''];

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * Adds a new child node.
     *
     * @param Node|null $n The node to add as a child. If null, a new Node will be created (and returned).
     *
     * @return Node Returns the given or newly created node, for use in fluent notations.
     */
    public function addChild(Node $n = null)
    {
        if ($n === null) {
            $n = new Node();
        }

        $this->children[] = $n;
        $n->setParent($this);
        $n->setTree($this->tree);

        return $n;
    }

    public function setTree(Tree $t)
    {
        $this->tree = $t;
    }

    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Adds this node to the Tree's node index, so that it can later be retrieved by calling \Webfactory\Bundle\NavigationBundle\Tree\Tree::find.
     *
     * @param array $requirements An array of key-value-pairs used for indexing. If a later call to \Webfactory\Bundle\NavigationBundle\Tree\Tree::find passes at least these key-value-pairs, the node will be found.
     *
     * @return $this The node itself, for use in fluent notations.
     */
    public function index(array $requirements)
    {
        $this->tree->addFindIndex($this, $requirements);

        return $this;
    }

    /** @deprecated */
    public function activateOn(array $requirements)
    {
        return $this->index($requirements);
    }

    /**
     * Sets a value on this node.
     *
     * @param string $name The value's name.
     * @param mixed $value The value's value.
     *
     * @return $this The node itself, for use in fluent notations.
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Gets a value stored in this node.
     *
     * @param string $name Name of the value to get.
     *
     * @return mixed|null The value, or null if the $name is unknown.
     */
    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Returns all data stored in this node.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return null|Node Returns the parent node.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Node[] The array of all Nodes from the root towards this node.
     */
    public function getPath()
    {
        if ($this->parent === null) {
            return [$this];
        } else {
            $p = $this->parent->getPath();
            $p[] = $this;

            return $p;
        }
    }

    /**
     * @return Node[] Returns all child nodes.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool Whether the node has child nodes or not.
     */
    public function hasChildren()
    {
        return (bool)$this->children;
    }

    /**
     * @return bool Whether the node has visible child nodes or not.
     */
    public function hasVisibleChildren()
    {
        foreach ($this->children as $childNode) {
            if ($childNode->get('visible') === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Node $ancestor
     *
     * @return bool True if the given node is an ancestor of the current node.
     */
    public function hasAncestor(Node $ancestor)
    {
        return $this->parent && (($this->parent === $ancestor) || $this->parent->hasAncestor($ancestor));
    }

    /**
     * @param Node $descendant
     *
     * @return bool True if $descendant is a descendant of the current node.
     */
    public function hasDescendant(Node $descendant)
    {
        return $descendant->hasAncestor($this);
    }

    /**
     * @return int Returns the level of this node, with "0" being the root level.
     */
    public function getLevel()
    {
        if ($this->parent) {
            return $this->parent->getLevel() + 1;
        } else {
            return 0;
        }
    }

    /**
     * Sets this node as the currently "active" (in terms of navigation state) node in the Tree.
     *
     * @return $this The node itself, for use in fluent notations.
     */
    public function setActive()
    {
        $this->tree->setActiveNode($this);

        return $this;
    }

    /**
     * Sets this node as the "closest to active" node in the Tree.
     *
     * Imagine, for example, a "gallery" page that shows some featured items from your shop
     * and also provides a form to filter or search for articles. When the user submits this
     * form, you show her a "results" page, but clearly this is not the same as your "gallery".
     *
     * So, you could use setActivePath() on the "gallery" node, so it remains highlighted in
     * the navigation. But, as it is not really the currently active page, it will only be
     * in "active path" state and, among other things, still provide a clickable link to return
     * to the gallery page.
     *
     * @return $this The node itself, for use in fluent notations.
     */
    public function setActivePath()
    {
        $this->tree->setActivePath($this);

        return $this;
    }

    /**
     * @return bool Whether this is the currently active node in the tree.
     */
    public function isActiveNode()
    {
        return $this->tree->getActiveNode() === $this;
    }

    /**
     * @return bool Whether this node lies on the path from the Tree root towards the active node.
     */
    public function isActivePath()
    {
        if (!($ap = $this->tree->getActivePath())) {
            return false;
        }

        return $this === $ap || $this->hasDescendant($ap);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);

        return $this;
    }

    protected function setParent(Node $p)
    {
        $this->parent = $p;
    }

}
