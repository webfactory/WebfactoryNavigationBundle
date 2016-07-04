<?php
namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class TreeCommand extends Command
{

    /**
     * @var Tree
     */
    protected $tree;

    public function __construct(Tree $tree)
    {
        parent::__construct();
        $this->tree = $tree;
    }

    protected function formatValue($value)
    {
        if (is_string($value) || is_int($value)) {
            return $value;
        } else if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return json_encode($value);
        }
    }
}
