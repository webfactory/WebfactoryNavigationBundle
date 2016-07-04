<?php
namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webfactory\Bundle\NavigationBundle\Tree\Node;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class DumpTreeCommand extends Command
{
    /**
     * @var Tree
     */
    private $tree;

    private $properties = ['caption', 'visible', 'url', 'route', 'routeParameters'];

    public function __construct(Tree $tree)
    {
        parent::__construct();
        $this->tree = $tree;
    }

     protected function configure()
     {
         $this
             ->setName('webfactory:navigation:dump-tree')
             ->setDescription('Dumps the current navigation tree');
     }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $roots = $this->tree->getRootNodes();

        foreach ($roots as $root) {
            $this->dumpNode($root, $output);
        }
    }

    private function dumpNode(Node $n, OutputInterface $output, $depth = 0)
    {
        $first = true;

        $children = $n->getChildren();

        foreach ($this->properties as $property) {
            $value = $n->get($property);

            if ($first) {
                $output->writeln(str_repeat(' |  ', $depth) . ' |');
                $sep = str_repeat(' |  ', $depth) . ' +-- ';
            } else {
                $sep = str_repeat(' |  ', $depth + 1) . ' ';
            }

            $output->writeln("$sep$property = {$this->formatValue($value)}");
            $first = false;

        }

        foreach ($children as $child) {
            $this->dumpNode($child, $output, $depth + 1);
        }
    }

    private function formatValue($value)
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
