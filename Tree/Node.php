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
    protected $children = array();

    /**
     * @var Node|null
     */
    protected $parent = null;

    protected $data = array('visible' => false, 'breadcrumbsVisible' => true, 'url' => false, 'caption' => '');

    /**
     * @var Tree
     */
    protected $tree;

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

    protected function setParent(Node $p)
    {
        $this->parent = $p;
    }

    public function setTree(Tree $t)
    {
        $this->tree = $t;
    }

    public function getTree()
    {
        return $this->tree;
    }

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

    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function get($name)
    {
        return @$this->data[$name];
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return (bool)$this->children;
    }

    /**
     * @return bool
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

    public function hasAncestor(Node $ancestor)
    {
        return $this->parent && (($this->parent === $ancestor) || $this->parent->hasAncestor($ancestor));
    }

    public function hasDescendant(Node $descendant)
    {
        return $descendant->hasAncestor($this);
    }

    public function getLevel()
    {
        if ($this->parent) {
            return $this->parent->getLevel() + 1;
        } else {
            return 0;
        }
    }

    public function setActive()
    {
        $this->tree->setActiveNode($this);

        return $this;
    }

    public function setActivePath()
    {
        $this->tree->setActivePath($this);

        return $this;
    }

    public function isActiveNode()
    {
        return $this->tree->getActiveNode() === $this;
    }

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

}
