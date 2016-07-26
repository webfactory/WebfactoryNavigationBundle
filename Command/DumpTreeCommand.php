<?php
namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webfactory\Bundle\NavigationBundle\Tree\Node;

class DumpTreeCommand extends TreeCommand
{
    protected function configure()
    {
        $this
            ->setName('webfactory:navigation:dump-tree')
            ->setDescription('Dumps the current navigation tree');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $roots = $this->getTree()->getRootNodes();

        foreach ($roots as $root) {
            $this->dumpNode($root, $output);
        }
    }

    private function dumpNode(Node $n, OutputInterface $output, $depth = 0)
    {
        $first = true;

        $children = $n->getChildren();

        foreach ($n->getData() as $property => $value) {
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
}
