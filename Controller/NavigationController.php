<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Webfactory\Bundle\NavigationBundle\Tree\Node;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

class NavigationController extends AbstractController
{
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            Tree::class,
        ]);
    }

    /**
     * Renders the navigation tree, starting at a given root node.
     *
     * @param array|Node $root           an array of key-values pairs that will be passed to the
     *                                   \Webfactory\Bundle\NavigationBundle\Tree\Tree::find method to look up the root
     *                                   node
     * @param int        $maxLevels      the maximum number of tree levels (starting from the specified root) to draw
     * @param int        $expandedLevels The number of levels to always draw expanded (i. e. showing all nodes).
     * @param string     $template       Reference to the Twig template to use
     *
     * @return Response
     */
    public function treeAction(
        $root,
        $maxLevels = 1,
        $expandedLevels = 1,
        $template = 'WebfactoryNavigationBundle:Navigation:navigation.html.twig'
    ) {
        if (\is_array($root)) {
            $node = $this->getTree()->find($root);
            if (!$node) {
                return new Response(' ## Navigation:tree($root => '.json_encode($root).' could not find the node ##');
            }
        } elseif (!($root instanceof Node)) {
            throw new \InvalidArgumentException("The 'root' parameter must either be an array or a tree Node.");
        } else {
            $node = $root;
        }

        return $this->render($template, [
            'node' => $node,
            'level' => 0,
            'maxLevels' => $maxLevels,
            'expandedLevels' => $expandedLevels,
        ]);
    }

    /**
     * Renders a part of the subtree that contains the currently active node.
     *
     * This is useful if you need to place differnent parts of your navigation in different places in your HTML.
     *
     * For example, in one location you might need the first two levels of navigation; you would use the
     * {@link NavigationController::treeAction} for that and provide a "root" node.
     *
     * Then, in another location, you'd need two additional levels (i. e. the 3rd and 4th levels). In this case, you
     * cannot use a fixed root node as the subtree that needs to be shown depends on which part of the tree your
     * currently "active" node is located.
     *
     * This is where this action comes into play: It will figure out the path from your currently active node towards
     * the root of the tree. It will then pick the ancestor node at the $startLevel level and use it as the root
     * for a tree $maxLevels deep and unconditionally expanded at the first $expandedLevels levels.
     *
     * @param        $startLevel     the level (counted from the root, which is 0) to start the tree at
     * @param int    $maxLevels      the maximum number of tree levels (starting from the specified root) to draw
     * @param int    $expandedLevels The number of levels to always draw expanded (i. e. showing all nodes).
     * @param string $template       Reference to the Twig template to use
     *
     * @return Response
     */
    public function ancestryAction($startLevel, $maxLevels = 1, $expandedLevels = 1, $template = 'WebfactoryNavigationBundle:Navigation:navigation.html.twig')
    {
        if ($node = $this->getTree()->getActivePath()) {
            $path = $node->getPath();

            if (isset($path[$startLevel])) {
                return $this->treeAction($path[$startLevel], $maxLevels, $expandedLevels, $template);
            }
        }

        return new Response();
    }

    /**
     * Renders a breadcrumb navigation for the currently active tree node.
     *
     * @param string $template Reference to the Twig template to use
     *
     * @return Response
     */
    public function breadcrumbsAction($template = 'WebfactoryNavigationBundle:Navigation:breadcrumbs.html.twig')
    {
        if ($node = $this->getTree()->getActivePath()) {
            return $this->render($template, [
                'breadcrumbs' => $node->getPath(),
            ]);
        } else {
            return new Response();
        }
    }

    private function getTree(): Tree
    {
        return $this->container->get(Tree::class);
    }
}
