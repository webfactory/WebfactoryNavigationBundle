<?php

namespace Webfactory\Bundle\NavigationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NavigationController extends Controller
{

    /** @return \Webfactory\Bundle\NavigationBundle\Tree\Tree */
    protected function getTree()
    {
        return $this->get('webfactory_navigation.tree');
    }

    public function treeAction(
        $root,
        $maxLevels = 1,
        $expandedLevels = 1,
        $template = 'WebfactoryNavigationBundle:Navigation:navigation.html.twig'
    ) {

        if ($node = $this->getTree()->find($root)) {
            return $this->render($template, array(
                'root' => $node,
                'maxLevels' => $maxLevels,
                'expandedLevels' => $expandedLevels
            ));
        } else {
            return new \Symfony\Component\HttpFoundation\Response(' ## Navigation:tree($root => '.json_encode($root).' could not find the node ##');
        }
    }

    public function breadcrumbsAction($template = 'WebfactoryNavigationBundle:Navigation:breadcrumbs.html.twig')
    {

        if ($node = $this->getTree()->getActivePath()) {
            $breadcrumbs = array($node);
            while ($node = $node->getParent()) {
                array_unshift($breadcrumbs, $node);
            }

            return $this->render($template, array(
                'breadcrumbs' => $breadcrumbs
            ));
        } else {
            return new \Symfony\Component\HttpFoundation\Response();
        }
    }

}
