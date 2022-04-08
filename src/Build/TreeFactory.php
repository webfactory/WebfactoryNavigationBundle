<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Build;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Webfactory\Bundle\NavigationBundle\Event\TreeInitializedEvent;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class TreeFactory implements ServiceSubscriberInterface
{
    /** @var ConfigCacheFactoryInterface */
    private $configCacheFactory;

    private $cacheFile;

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

    public static function getSubscribedServices()
    {
        return [
            BuildDispatcher::class,
        ];
    }

    public function __construct(
        ConfigCacheFactoryInterface $configCacheFactory,
        $cacheFile,
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null,
        Stopwatch $stopwatch = null
    ) {
        $this->configCacheFactory = $configCacheFactory;
        $this->cacheFile = $cacheFile;
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    public function debug($msg)
    {
        if ($this->logger) {
            $this->logger->debug("$msg (PID ".getmypid().', microtime '.microtime().')');
        }
    }

    /**
     * @param $sectionName
     *
     * @return StopwatchEvent|null
     */
    protected function startTiming($sectionName)
    {
        if ($this->stopwatch) {
            return $this->stopwatch->start('webfactory/navigation-bundle: '.$sectionName);
        }

        return null;
    }

    protected function stopTiming(StopwatchEvent $watch = null)
    {
        if ($watch) {
            $watch->stop();
        }
    }

    /**
     * @return Tree
     */
    public function getTree()
    {
        if (!$this->_tree) {
            $self = $this;
            $cache = $this->configCacheFactory->cache($this->cacheFile, function (ConfigCacheInterface $cache) use ($self) {
                $self->debug('Building the tree');
                $self->buildTreeCache($cache);
                $self->debug('Finished building the tree');
            });

            if (!$this->_tree) {
                $this->debug('Loading the cached tree');
                $_watch = $this->startTiming('Loading a cached tree');
                $this->_tree = require $cache->getPath();
                $this->stopTiming($_watch);
                $this->debug('Finished loading the cached tree');
            }

            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch(
                    new TreeInitializedEvent($this->_tree),
                    'webfactory_navigation.tree_initialized'
                );
            }
        }

        return $this->_tree;
    }

    public function buildTreeCache(ConfigCacheInterface $cache)
    {
        $this->_tree = new Tree();
        // Dynamic (runtime) lookup:
        $dispatcher = $this->container->get(BuildDispatcher::class);
        $dispatcher->start($this->_tree);
        $cache->write("<?php return unserialize(<<<EOD\n".serialize($this->_tree)."\nEOD\n);",
            $dispatcher->getResources());
    }
}
