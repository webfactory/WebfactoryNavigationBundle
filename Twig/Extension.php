<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Webfactory\Bundle\NavigationBundle\Navigation\NodeInterface;
use Webfactory\Bundle\NavigationBundle\Navigation\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Extension extends \Twig_Extension {

    protected $urlGenerators = array();

    public function getName() {
        return 'webfactory_navigation_twig_extension';
    }

    public function addUrlGenerator(UrlGeneratorInterface $urlGenerator) {
        $this->urlGenerators[] = $urlGenerator;
    }

    public function getFunctions() {
        return array(
            'navigationNodePath' => new \Twig_Function_Method($this, 'getNavigationNodePath')
        );
    }

    public function getNavigationNodePath(NodeInterface $node) {
        foreach ($this->urlGenerators as $urlGenerator) {
            try {

                return $urlGenerator->generateNavigationNodePath($node);

            } catch (RouteNotFoundException $e) {}
        }
        throw new RouteNotFoundException();
    }

}