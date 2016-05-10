<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Webfactory\Bundle\NavigationBundle\Navigation\NodeInterface;
use Webfactory\Navigation\NavigationInterface;

class Extension extends \Twig_Extension {

    protected $resources;

    /** @var  \Twig_Template */
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
        return [
            new \Twig_SimpleFunction('navigation', [$this, 'renderNavigation'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_level', [$this, 'renderNavigationLevel'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_level_class', [$this, 'renderNavigationLevelClass'], ['needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_container', [$this, 'renderNavigationContainer'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_container_class', [$this, 'renderNavigationContainerClass'], ['needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_link', [$this, 'renderNavigationLink'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_link_class', [$this, 'renderNavigationLinkClass'], ['needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_url', [$this, 'renderNavigationUrl'], ['needs_environment' => true]),
            new \Twig_SimpleFunction('navigation_text', [$this, 'renderNavigationText'], ['is_safe' => ['all'], 'needs_environment' => true]),
        ];
    }

    public function renderNavigation(\Twig_Environment $env, NavigationInterface $navigation) {
        return $this->renderNavigationLevel($env, $navigation, $navigation->getRootNodes(), 0, null);
    }

    public function renderNavigationLevel(\Twig_Environment $env, NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($env, $navigation, 'navigation_level', $variables);
    }

    public function renderNavigationLevelClass(\Twig_Environment $env, NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($env, $navigation, 'navigation_level_class', $variables);
    }

    public function renderNavigationContainer(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($env, $navigation, 'navigation_container', $variables);
    }

    public function renderNavigationContainerClass(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($env, $navigation, 'navigation_container_class', $variables);
    }

    public function renderNavigationLink(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($env, $navigation, 'navigation_link', $variables);
    }

    public function renderNavigationLinkClass(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($env, $navigation, 'navigation_link_class', $variables);
    }

    public function renderNavigationUrl(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($env, $navigation, 'navigation_url', $variables);
    }

    public function renderNavigationText(\Twig_Environment $env, NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($env, $navigation, 'navigation_text', $variables);
    }

    public function renderBlock(\Twig_Environment $env, $navigation, $name, array $variables) {
        if (!$this->template) {
            $this->template = $env->loadTemplate(reset($this->resources));
        }

        $blocks = $this->getBlocks($env, $navigation);

        ob_start();
        $this->template->displayBlock($name, $variables, $blocks);
        $html = ob_get_clean();

        return $html;
    }

    protected function getBlocks(\Twig_Environment $env, NavigationInterface $navigation) {
        if (!$this->blocks->contains($navigation)) {

            $resources = $this->resources;

            if (isset($this->themes[$navigation])) {
                $resources = array_merge($resources, $this->themes[$navigation]);
            }

            $blocks = array();

            foreach ($resources as $resource) {
                if (!$resource instanceof \Twig_Template) {
                    $resource = $env->loadTemplate($resource);
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
