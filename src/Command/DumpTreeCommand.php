<?php

namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webfactory\Bundle\NavigationBundle\Tree\Node;

class DumpTreeCommand extends TreeCommand
{
    private $shortOutput = false;

    protected function configure()
    {
        $this
            ->setName('webfactory:navigation:dump-tree')
            ->setDescription('Dumps the current navigation tree')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Kompakte Ausgabe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->shortOutput = $input->getOption('short');

        $roots = $this->getTree()->getRootNodes();

        foreach ($roots as $root) {
            $this->dumpNode($root, $output);
        }

        return 0;
    }

    private function dumpNode(Node $n, OutputInterface $output, $depth = 0): void
    {
        $first = true;

        $children = $n->getChildren();

        if (!$this->shortOutput) {
            foreach ($n->getData() as $property => $value) {
                if ($first) {
                    $output->writeln(str_repeat(' |  ', $depth).' |');
                    $sep = str_repeat(' |  ', $depth).' +-- ';
                } else {
                    $sep = str_repeat(' |  ', $depth + 1).' ';
                }

                $output->writeln("$sep$property = {$this->formatValue($value)}");
                $first = false;
            }
        } else {
            $mode = $n->get('visible') ? 'info' : 'comment';
            $sep = str_repeat(' |  ', $depth).' +-- ';
            $output->writeln("<$mode>$sep{$n->get('caption')} [{$n->get('url')}]</$mode>");
        }

        foreach ($children as $child) {
            $this->dumpNode($child, $output, $depth + 1);
        }
    }
}
