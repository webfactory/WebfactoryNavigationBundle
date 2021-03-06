<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class TreeInitializedEvent extends Event
{
    /** @var Tree */
    protected $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return Tree
     */
    public function getTree()
    {
        return $this->tree;
    }
}
