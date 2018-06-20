<?php
namespace Webfactory\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LookupNodeCommand extends TreeCommand
{
     protected function configure()
     {
         $this
             ->setName('webfactory:navigation:lookup-node')
             ->setDescription('Looks up a node in the tree')
             ->addArgument('queryParam', InputArgument::IS_ARRAY, 'One or several key=value pairs to search in the node index');
     }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provisions = [];

        foreach ($input->getArgument('queryParam') as $param) {
            $i = strpos($param, '=');
            $key = substr($param, 0, $i);
            $value = substr($param, $i+1);
            $provisions[$key] = $value;
        }

        if ($node = $this->getTree()->find($provisions)) {
            $output->writeln("Found a matching node:");
            foreach ($node->getData() as $key => $value) {
                $output->writeln("\t$key = {$this->formatValue($value)}");
            }
        } else {
            $output->writeln("No matching node found.");
        }
    }
}
