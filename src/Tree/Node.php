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
     * @return Node returns the given or newly created node, for use in fluent notations
     */
    public function addChild(self $n = null)
    {
        if (null === $n) {
            $n = new self();
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
     * @return $this the node itself, for use in fluent notations
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
     * @param string $name  the value's name
     * @param mixed  $value the value's value
     *
     * @return $this the node itself, for use in fluent notations
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Gets a value stored in this node.
     *
     * @param string $name name of the value to get
     *
     * @return mixed|null the value, or null if the $name is unknown
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
     * @return Node|null returns the parent node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Node[] the array of all Nodes from the root towards this node
     */
    public function getPath()
    {
        if (null === $this->parent) {
            return [$this];
        } else {
            $p = $this->parent->getPath();
            $p[] = $this;

            return $p;
        }
    }

    /**
     * @return Node[] returns all child nodes
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool whether the node has child nodes or not
     */
    public function hasChildren()
    {
        return (bool) $this->children;
    }

    /**
     * @return bool whether the node has visible child nodes or not
     */
    public function hasVisibleChildren()
    {
        foreach ($this->children as $childNode) {
            if (true === $childNode->get('visible')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Node $ancestor
     *
     * @return bool true if the given node is an ancestor of the current node
     */
    public function hasAncestor(self $ancestor)
    {
        return $this->parent && (($this->parent === $ancestor) || $this->parent->hasAncestor($ancestor));
    }

    /**
     * @param Node $descendant
     *
     * @return bool true if $descendant is a descendant of the current node
     */
    public function hasDescendant(self $descendant)
    {
        return $descendant->hasAncestor($this);
    }

    /**
     * @return int returns the level of this node, with "0" being the root level
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
     * @return $this the node itself, for use in fluent notations
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
     * @return $this the node itself, for use in fluent notations
     */
    public function setActivePath()
    {
        $this->tree->setActivePath($this);

        return $this;
    }

    /**
     * @return bool whether this is the currently active node in the tree
     */
    public function isActiveNode()
    {
        return $this->tree->getActiveNode() === $this;
    }

    /**
     * @return bool whether this node lies on the path from the Tree root towards the active node
     */
    public function isActivePath()
    {
        if (!($ap = $this->tree->getActivePath())) {
            return false;
        }

        return $this === $ap || $this->hasDescendant($ap);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    protected function setParent(self $p)
    {
        $this->parent = $p;
    }
}
