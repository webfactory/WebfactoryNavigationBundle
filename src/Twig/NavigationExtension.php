<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webfactory\Bundle\NavigationBundle\Tree\Node;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class NavigationExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    public static function getSubscribedServices(): array
    {
        return [Tree::class];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('navigation_tree', [$this, 'renderTree'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('navigation_ancestry', [$this, 'renderAncestry'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('navigation_breadcrumbs', [$this, 'renderBreadcrumbs'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new TwigFunction('navigation_active_at_level', [$this, 'getNavigationActiveAtLevel']),
            new TwigFunction('navigation_find', [$this, 'findNode']),
            new TwigFunction('navigation_active_node', [$this, 'getActiveNode']),
            new TwigFunction('navigation_active_path', [$this, 'getActivePath']),
            new TwigFunction('power_set', [$this, 'getPowerSet']),
        ];
    }

    /**
     * Renders the navigation tree, starting at a root node.
     *
     * @param array|Node $root           Root node or array of key-values pairs that will be passed to the
     *                                   \Webfactory\Bundle\NavigationBundle\Tree\Tree::find method to look it up
     * @param int        $maxLevels      Maximum number of tree levels (starting from the specified root) to draw
     * @param int        $expandedLevels Number of levels to always draw expanded (i. e. showing all nodes).
     */
    public function renderTree(
        Environment $environment,
        $root,
        int $maxLevels = 1,
        int $expandedLevels = 1,
        string $template = '@WebfactoryNavigation/Navigation/navigation.html.twig'
    ): string {
        if (!($root instanceof Node)) {
            $root = $this->getTree()->find($root);
        }

        if (null === $root) {
            return ' ## navigation_tree: root not found ##';
        }

        return $environment->render(
            $template,
            [
                'root' => $root,
                'level' => 0,
                'maxLevels' => $maxLevels,
                'expandedLevels' => $expandedLevels,
            ]
        );
    }

    private function getTree(): Tree
    {
        return $this->container->get(Tree::class);
    }

    /**
     * Renders a part of the subtree that contains the currently active node.
     *
     * This is useful if you need to place different parts of your navigation in different places in your HTML.
     *
     * For example, in one location you might need the first two levels of navigation; you would use the
     * {@link NavigationController::navigation_tree} for that and provide a "root" node.
     *
     * Then, in another location, you'd need two additional levels (i. e. the 3rd and 4th levels). In this case, you
     * cannot use a fixed root node as the subtree that needs to be shown depends on which part of the tree your
     * currently "active" node is located.
     *
     * This is where this action comes into play: It will figure out the path from your currently active node towards
     * the root of the tree. It will then pick the ancestor node at the $startLevel level and use it as the root
     * for a tree $maxLevels deep and unconditionally expanded at the first $expandedLevels levels.
     *
     * @param int $startLevel     Level (counted from the root, which is 0) to start the tree at
     * @param int $maxLevels      Maximum number of tree levels (starting from the specified root) to draw
     * @param int $expandedLevels Number of levels to always draw expanded (i. e. showing all nodes).
     */
    public function renderAncestry(
        Environment $environment,
        int $startLevel,
        int $maxLevels = 1,
        int $expandedLevels = 1,
        string $template = '@WebfactoryNavigation/Navigation/navigation.html.twig'
    ): string {
        $node = $this->getTree()->getActivePath();
        if (null === $node) {
            return '';
        }

        $path = $node->getPath();
        if (isset($path[$startLevel])) {
            return $this->renderTree($environment, $path[$startLevel], $maxLevels, $expandedLevels, $template);
        }

        return '';
    }

    public function renderBreadcrumbs(
        Environment $environment,
        string $template = '@WebfactoryNavigation/Navigation/breadcrumbs.html.twig'
    ): string {
        $node = $this->getTree()->getActivePath();
        if (null === $node) {
            return '';
        }

        return $environment->render($template, ['breadcrumbs' => $node->getPath()]);
    }

    public function getActiveNode(): ?Node
    {
        return $this->getTree()->getActiveNode();
    }

    public function getActivePath(): ?Node
    {
        return $this->getTree()->getActivePath();
    }

    /**
     * Returns the navigation node which lies on the currently active path at the given level.
     *
     * @param int|null $level The level of the node to be returned
     */
    public function getNavigationActiveAtLevel(?int $level): ?Node
    {
        $activeNode = $this->getTree()->getActiveNode();
        if (!$activeNode) {
            return null;
        }

        $path = $activeNode->getPath();

        if (isset($path[$level])) {
            return $path[$level];
        }

        return null;
    }

    /**
     * Finds a node indexed in the tree. See \Webfactory\Bundle\NavigationBundle\Tree\Tree::find.
     *
     * @param array $provisions parameters used to look up the node
     */
    public function findNode(array $provisions): ?Node
    {
        return $this->getTree()->find($provisions);
    }

    public function getPowerSet(array $baseSet)
    {
        $count = \count($baseSet);
        $members = 2 ** $count;
        $powerSet = [];
        for ($i = 0; $i < $members; ++$i) {
            $b = sprintf('%0'.$count.'b', $i);
            $out = [];
            for ($j = 0; $j < $count; ++$j) {
                if ('1' == $b[$j]) {
                    $out[] = $baseSet[$j];
                }
            }
            $powerSet[] = $out;
        }

        return $powerSet;
    }
}
