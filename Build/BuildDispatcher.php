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
    protected $directors = [];
    protected $resources = [];
    protected $queue;

    public function addDirector(BuildDirector $director, $priority = 100): void
    {
        $this->directors[$priority][] = $director;
    }

    public function start(Tree $tree): void
    {
        $directorsOrderedByPriority = $this->getDirectorsOrderedByPriority();
        $this->queue = [new BuildContext([])];
        while ($context = array_shift($this->queue)) {
            foreach ($directorsOrderedByPriority as $director) {
                $director->build($context, $tree, $this);
            }
        }
    }

    public function search(BuildContext $context): void
    {
        $this->queue[] = $context;
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->resources[] = $resource;
    }

    public function getResources(): array
    {
        return array_unique($this->resources);
    }

    /**
     * @return BuildDirector[]
     */
    private function getDirectorsOrderedByPriority(): array
    {
        krsort($this->directors, SORT_NUMERIC);
        $buildDirectorsOrderedByPriority = [];

        foreach ($this->directors as $directors) {
            $buildDirectorsOrderedByPriority = array_merge($buildDirectorsOrderedByPriority, $directors);
        }

        return $buildDirectorsOrderedByPriority;
    }
}
