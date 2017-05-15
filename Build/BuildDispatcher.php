<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Build;

use Symfony\Component\Config\Resource\ResourceInterface;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class BuildDispatcher
{
    protected $directors = array();
    protected $resources = array();
    protected $queue;

    public function addDirector(BuildDirector $m, $priority = 100)
    {
        // TODO: Implement priority handling
        $this->directors[] = $m;
    }

    public function start(Tree $tree)
    {
        $this->queue = array(new BuildContext(array()));
        while ($c = array_shift($this->queue)) {
            foreach ($this->directors as $m) {
                $m->build($c, $tree, $this);
            }
        }
    }

    public function search(BuildContext $c)
    {
        $this->queue[] = $c;
    }

    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    public function getResources()
    {
        return array_unique($this->resources);
    }

}
