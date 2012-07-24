<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Webfactory\Bundle\NavigationBundle\Navigation\NodeInterface;
use Webfactory\Bundle\NavigationBundle\Navigation\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Webfactory\Navigation\NavigationInterface;

class Extension extends \Twig_Extension {

    protected $environment;
    protected $resources;
    protected $template;
    protected $themes;
    protected $blocks;

    public function __construct(array $resources = array()) {
        $this->resources = $resources;
        $this->themes = new \SplObjectStorage();
        $this->blocks = new \SplObjectStorage();
    }

    public function getName() {
        return 'webfactory_navigation_twig_extension';
    }

    public function initRuntime(\Twig_Environment $environment) {
        $this->environment = $environment;
    }

    public function getTokenParsers() {
        return array(
            new NavigationThemeTokenParser(),
        );
    }

    public function setTheme(NavigationInterface $navigation, array $resources) {
        $this->themes->attach($navigation, $resources);
        $this->blocks = new \SplObjectStorage();
    }

    public function getFunctions() {
        return array(
            'navigation' =>  new \Twig_Function_Method($this, 'renderNavigation', array('is_safe' => array('all'))),
            'navigation_level' => new \Twig_Function_Method($this, 'renderNavigationLevel', array('is_safe' => array('all'))),
            'navigation_level_class' => new \Twig_Function_Method($this, 'renderNavigationLevelClass'),
            'navigation_container' => new \Twig_Function_Method($this, 'renderNavigationContainer', array('is_safe' => array('all'))),
            'navigation_container_class' => new \Twig_Function_Method($this, 'renderNavigationContainerClass'),
            'navigation_link' => new \Twig_Function_Method($this, 'renderNavigationLink', array('is_safe' => array('all'))),
            'navigation_link_class' => new \Twig_Function_Method($this, 'renderNavigationLinkClass'),
            'navigation_url' => new \Twig_Function_Method($this, 'renderNavigationUrl'),
            'navigation_text' => new \Twig_Function_Method($this, 'renderNavigationText', array('is_safe' => array('all')))
        );
    }

    public function renderNavigation(NavigationInterface $navigation) {
        return $this->renderNavigationLevel($navigation, $navigation->getRootNodes(), 0, null);
    }

    public function renderNavigationLevel(NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($navigation, 'navigation_level', $variables);
    }

    public function renderNavigationLevelClass(NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($navigation, 'navigation_level_class', $variables);
    }

    public function renderNavigationContainer(NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($navigation, 'navigation_container', $variables);
    }

    public function renderNavigationContainerClass(NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($navigation, 'navigation_container_class', $variables);
    }

    public function renderNavigationLink(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_link', $variables);
    }

    public function renderNavigationLinkClass(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_link_class', $variables);
    }

    public function renderNavigationUrl(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_url', $variables);
    }

    public function renderNavigationText(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_text', $variables);
    }

    public function renderBlock($navigation, $name, array $variables) {
        if (!$this->template) {
            $this->template = $this->environment->loadTemplate(reset($this->resources));
        }

        $blocks = $this->getBlocks($navigation);

        ob_start();
        $this->template->displayBlock($name, $variables, $blocks);
        $html = ob_get_clean();

        return $html;
    }

    protected function getBlocks(NavigationInterface $navigation) {
        if (!$this->blocks->contains($navigation)) {

            $resources = $this->resources;

            if (isset($this->themes[$navigation])) {
                $resources = array_merge($resources, $this->themes[$navigation]);
            }

            $blocks = array();

            foreach ($resources as $resource) {
                if (!$resource instanceof \Twig_Template) {
                    $resource = $this->environment->loadTemplate($resource);
                }
                $resourceBlocks = array();
                do {
                    $resourceBlocks = array_merge($resource->getBlocks(), $resourceBlocks);
                } while (false !== $resource = $resource->getParent(array()));
                $blocks = array_merge($blocks, $resourceBlocks);
            }

            $this->blocks->attach($navigation, $blocks);
        } else {
            $blocks = $this->blocks[$navigation];
        }
        return $blocks;
    }

}