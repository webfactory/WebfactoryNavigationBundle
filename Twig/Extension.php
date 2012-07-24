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
            'power_set' => new \Twig_Function_Method($this, 'getPowerSet'),
            'navigation' =>  new \Twig_Function_Method($this, 'renderNavigation', array('is_safe' => array('all'))),
            'navigation_list' => new \Twig_Function_Method($this, 'renderNavigationList', array('is_safe' => array('all'))),
            'navigation_list_class' => new \Twig_Function_Method($this, 'renderNavigationListClass'),
            'navigation_item' => new \Twig_Function_Method($this, 'renderNavigationItem', array('is_safe' => array('all'))),
            'navigation_item_class' => new \Twig_Function_Method($this, 'renderNavigationItemClass'),
            'navigation_text' => new \Twig_Function_Method($this, 'renderNavigationText', array('is_safe' => array('all'))),
            'navigation_text_class' => new \Twig_Function_Method($this, 'renderNavigationTextClass'),
            'navigation_url' => new \Twig_Function_Method($this, 'renderNavigationUrl'),
            'navigation_caption' => new \Twig_Function_Method($this, 'renderNavigationCaption', array('is_safe' => array('all')))
        );
    }

    public function getPowerSet(array $baseSet) {
        $count = count($baseSet);
        $members = pow(2, $count);
        $powerSet = array();
        for ($i = 0; $i < $members; $i++) {
            $b = sprintf("%0".$count."b", $i);
            $out = array();
            for ($j = 0; $j < $count; $j++) {
                if ($b{$j} == '1') $out[] = $baseSet[$j];
            }
            $powerSet[] = $out;
        }
        return $powerSet;
    }

    public function renderNavigation(NavigationInterface $navigation) {
        return $this->renderNavigationList($navigation, $navigation->getRootNodes(), 0, null);
    }

    public function renderNavigationList(NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($navigation, 'navigation_list', $variables);
    }

    public function renderNavigationListClass(NavigationInterface $navigation, array $nodes, $level, NodeInterface $parentNode = null) {
        $variables = array(
            'navigation' => $navigation,
            'nodes' => $nodes,
            'level' => $level,
            'parentNode' => $parentNode
        );

        return $this->renderBlock($navigation, 'navigation_list_class', $variables);
    }

    public function renderNavigationItem(NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($navigation, 'navigation_item', $variables);
    }

    public function renderNavigationItemClass(NavigationInterface $navigation, NodeInterface $node, $level, $loop) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level,
            'loop' => $loop
        );

        return $this->renderBlock($navigation, 'navigation_item_class', $variables);
    }

    public function renderNavigationText(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_text', $variables);
    }

    public function renderNavigationTextClass(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_text_class', $variables);
    }

    public function renderNavigationUrl(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_url', $variables);
    }

    public function renderNavigationCaption(NavigationInterface $navigation, NodeInterface $node, $level) {
        $variables = array(
            'navigation' => $navigation,
            'node' => $node,
            'level' => $level
        );

        return $this->renderBlock($navigation, 'navigation_caption', $variables);
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