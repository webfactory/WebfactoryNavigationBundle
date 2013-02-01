<?php

namespace Webfactory\Bundle\NavigationBundle\Build;
use Webfactory\Bundle\WfdMetaBundle\Provider;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCache;
use Webfactory\Bundle\WfdMetaBundle\Util\ExpirableConfigCache;

class TreeFactory {

    protected $metaProvider;
    protected $container;
    protected $logger;
    protected $tableDeps = array();
    protected $_tree;

    public function __construct(Provider $metaProvider, ContainerInterface $container, LoggerInterface $logger) {
        $this->metaProvider = $metaProvider;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function addTableDependency($tables) {
        $this->tableDeps += array_fill_keys((array)$tables, true);
    }

    public function debug($msg) {
        if ($this->logger)
            $this->logger->debug("$msg (PID " . getmypid() . ", microtime " . microtime() . ")");
    }

    public function getTree() {
        if (!$this->_tree) {
            $container = $this->container;
            $ts = $this->metaProvider->getLastTouched(array_keys($this->tableDeps));

            $cache = new ExpirableConfigCache(
                    $container->getParameter('kernel.cache_dir') . "/webfactory_navigation/tree.php",
                    $container->getParameter('kernel.debug'),
                    $ts
            );

            if ($cache->isFresh()) {
                $this->debug("Using a cached tree");
                $this->_tree = require $cache;
            } else {

                $cs = new \Webfactory\Bundle\WfdMetaBundle\Util\CriticalSection();
                $cs->setLogger($this->logger);

                $self = $this;
                $cs->execute(__FILE__, function () use ($self, $cache) {

                    if (!$cache->isFresh()) {
                        $self->dumpIntoCache($cache);
                    } else {
                        $self->debug("Using a cached tree, but had to wait for it to be built by someone else");
                        $this->_tree = require $cache;
                    }
                });
            }
        }

        return $this->_tree;
    }

    public function dumpIntoCache(ConfigCache $cache) {
        $this->debug("Building the tree");
        $this->_tree = new Tree();
        // Dynamic (runtime) lookup:
        $dispatcher = $this->container->get('webfactory_navigation.tree_factory.dispatcher');
        $dispatcher->start($this->_tree);
        $cache->write("<?php return unserialize(<<<EOD\n" . serialize($this->_tree) . "\nEOD\n);", $dispatcher->getResources());
        $this->debug("Finished building the tree");
    }


}
