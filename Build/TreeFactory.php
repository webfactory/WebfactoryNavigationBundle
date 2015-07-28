F<?php

namespace Webfactory\Bundle\NavigationBundle\Build;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Webfactory\Bundle\NavigationBundle\Event\TreeInitializedEvent;
use Webfactory\Bundle\WfdMetaBundle\Provider;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCache;
use Webfactory\Bundle\WfdMetaBundle\Util\ExpirableConfigCache;
use Webfactory\Bundle\WfdMetaBundle\MetaQuery;

class TreeFactory
{

    protected $cacheFile;
    protected $debug;

    /** @var MetaQuery */
    protected $metaQuery;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Tree */
    protected $_tree;

    /** @var Stopwatch */
    protected $stopwatch;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(
        $cacheFile,
        $debug,
        MetaQuery $metaQuery,
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null,
        Stopwatch $stopwatch = null
    ) {
        $this->cacheFile = $cacheFile;
        $this->debug = $debug;
        $this->metaQuery = $metaQuery;
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    public function addTableDependency($tables)
    {
        $this->metaQuery->addTable($tables);
    }

    public function debug($msg)
    {
        if ($this->logger) {
            $this->logger->debug("$msg (PID ".getmypid().", microtime ".microtime().")");
        }
    }

    protected function startTiming($sectionName)
    {
        if ($this->stopwatch) {
            return $this->stopwatch->start("webfactory/navigation-bundle: ".$sectionName);
        }
    }

    protected function stopTiming($watch)
    {
        if ($watch) {
            $watch->stop();
        }
    }

    public function getTree()
    {
        if (!$this->_tree) {

            $cache = new ExpirableConfigCache(
                $this->cacheFile,
                $this->debug,
                $this->metaQuery->getLastTouched()
            );

            $_watch = $this->startTiming('Checking whether the cache is fresh');
            $fresh = $cache->isFresh();
            $this->stopTiming($_watch);

            if (!$fresh) {

                $cs = new \Webfactory\Bundle\WfdMetaBundle\Util\CriticalSection();
                $cs->setLogger($this->logger);

                $_watch = $this->startTiming('Critical section');

                $self = $this;
                $cs->execute(__FILE__, function () use ($self, $cache) {

                    if (!$cache->isFresh()) {
                        $self->debug("Building the tree");
                        $self->buildTreeCache($cache);
                        $self->debug("Finished building the tree");
                    } else {
                        $self->debug("Had to wait for the cache to be initialized by another process");
                    }
                });

                $this->stopTiming($_watch);
            }

            if (!$this->_tree) {
                $this->debug("Loading the cached tree");
                $_watch = $this->startTiming('Loading a cached tree');
                $this->_tree = require $cache;
                $this->stopTiming($_watch);
                $this->debug("Finished loading the cached tree");
            }

            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch('webfactory_navigation.tree_initialized',
                    new TreeInitializedEvent($this->_tree));
            }
        }

        return $this->_tree;
    }

    public function buildTreeCache(ConfigCache $cache)
    {
        $this->_tree = new Tree();
        // Dynamic (runtime) lookup:
        $dispatcher = $this->container->get('webfactory_navigation.tree_factory.dispatcher');
        $dispatcher->start($this->_tree);
        $cache->write("<?php return unserialize(<<<EOD\n".serialize($this->_tree)."\nEOD\n);",
            $dispatcher->getResources());
    }

}
