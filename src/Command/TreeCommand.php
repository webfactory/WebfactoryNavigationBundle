<?php

namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Webfactory\Bundle\NavigationBundle\Build\TreeFactory;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class TreeCommand extends Command
{
    /**
     * @var TreeFactory
     */
    protected $treeFactory;

    public function __construct(TreeFactory $treeFactory)
    {
        parent::__construct();
        $this->treeFactory = $treeFactory;
    }

    /**
     * @return Tree
     */
    protected function getTree()
    {
        return $this->treeFactory->getTree();
    }

    protected function formatValue($value)
    {
        if (\is_string($value) || \is_int($value)) {
            return $value;
        } elseif (\is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return json_encode($value);
        }
    }
}
