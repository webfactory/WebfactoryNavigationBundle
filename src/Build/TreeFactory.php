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

final class TreeFactory implements ServiceSubscriberInterface
{
    /** @var ConfigCacheFactoryInterface */
    private $configCacheFactory;

    private $cacheFile;

    /** @var LoggerInterface */
    private $logger;

    /** @var Tree */
    private $_tree;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ContainerInterface */
    private $container;

    public static function getSubscribedServices(): array
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

    public function debug(string $msg): void
    {
        if ($this->logger) {
            $this->logger->debug("$msg (PID ".getmypid().', microtime '.microtime().')');
        }
    }

    private function startTiming(string $sectionName): ?StopwatchEvent
    {
        if ($this->stopwatch) {
            return $this->stopwatch->start('webfactory/navigation-bundle: '.$sectionName);
        }

        return null;
    }

    private function stopTiming(StopwatchEvent $watch = null): void
    {
        if ($watch) {
            $watch->stop();
        }
    }

    public function getTree(): Tree
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

    public function buildTreeCache(ConfigCacheInterface $cache): void
    {
        $this->_tree = new Tree();
        // Dynamic (runtime) lookup:
        $dispatcher = $this->container->get(BuildDispatcher::class);
        $dispatcher->start($this->_tree);
        $cache->write("<?php return unserialize(<<<EOD\n".serialize($this->_tree)."\nEOD\n);",
            $dispatcher->getResources());
    }
}
