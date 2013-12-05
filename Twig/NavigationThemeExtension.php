<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;
use Webfactory\Bundle\NavigationBundle\Tree\Node;

class NavigationThemeExtension extends \Twig_Extension {

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
        return 'webfactory_navigation_theme_extension';
    }

    public function initRuntime(\Twig_Environment $environment) {
        $this->environment = $environment;
    }

    public function getTokenParsers() {
        return array(
            new NavigationThemeTokenParser(),
        );
    }

    public function setTheme(Node $themeRoot, array $resources) {
        $this->themes->attach($themeRoot, $resources);
        $this->blocks = new \SplObjectStorage();
    }

    public function getFunctions() {
        return array(
            'power_set' => new \Twig_Function_Method($this, 'getPowerSet'),
            'navigation' =>  new \Twig_Function_Method($this, 'renderNavigation', array('is_safe' => array('all'))),
            'navigation_list' => new \Twig_Function_Method($this, 'renderNavigationList', array('is_safe' => array('all'))),
            'navigation_list_class' => new \Twig_Function_Method($this, 'renderNavigationListClass', array('is_safe' => array('all'))),
            'navigation_item' => new \Twig_Function_Method($this, 'renderNavigationItem', array('is_safe' => array('all'))),
            'navigation_item_class' => new \Twig_Function_Method($this, 'renderNavigationItemClass', array('is_safe' => array('all'))),
            'navigation_text' => new \Twig_Function_Method($this, 'renderNavigationText', array('is_safe' => array('all'))),
            'navigation_text_class' => new \Twig_Function_Method($this, 'renderNavigationTextClass', array('is_safe' => array('all'))),
            'navigation_url' => new \Twig_Function_Method($this, 'renderNavigationUrl', array('is_safe' => array('all'))),
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

    public function renderNavigation(Node $node, $maxLevels, $expandedLevels) {
        return $this->renderNavigationList($node, $node->getChildren(), 0, $maxLevels, $expandedLevels, $node);
    }

    public function renderNavigationList(Node $themeRoot, array $nodes, $level, $maxLevels, $expandedLevels, Node $parentNode = null) {
        if ($nodes && $level < $maxLevels && ($level < $expandedLevels || $parentNode->isActivePath())) {
            return $this->renderBlock($themeRoot, 'navigation_list', get_defined_vars());
        }
    }

    public function renderNavigationListClass(Node $themeRoot, array $nodes, $level, Node $parentNode = null) {
        return $this->renderBlock($themeRoot, 'navigation_list_class', get_defined_vars());
    }

    public function renderNavigationItem(Node $themeRoot, Node $node, $level, $maxLevels, $expandedLevels, $loop) {
        return $this->renderBlock($themeRoot, 'navigation_item', get_defined_vars());
    }

    public function renderNavigationItemClass(Node $themeRoot, Node $node, $level, $loop) {
        return $this->renderBlock($themeRoot, 'navigation_item_class', get_defined_vars());
    }

    public function renderNavigationText(Node $themeRoot, Node $node, $level) {
        return $this->renderBlock($themeRoot, 'navigation_text', get_defined_vars());
    }

    public function renderNavigationTextClass(Node $themeRoot, Node $node, $level) {
        return $this->renderBlock($themeRoot, 'navigation_text_class', get_defined_vars());
    }

    public function renderNavigationUrl(Node $themeRoot, Node $node, $level) {
        return $this->renderBlock($themeRoot, 'navigation_url', get_defined_vars());
    }

    public function renderNavigationCaption(Node $themeRoot, Node $node, $level) {
        return $this->renderBlock($themeRoot, 'navigation_caption', get_defined_vars());
    }

    public function renderBlock($themeRoot, $name, array $variables) {
        if (!$this->template) {
            $this->template = $this->environment->loadTemplate(reset($this->resources));
        }

        $blocks = $this->getBlocks($themeRoot);

        return $this->template->renderBlock($name, $variables, $blocks);
    }

    protected function getBlocks(Node $themeRoot) {
        if (!$this->blocks->contains($themeRoot)) {

            $resources = $this->resources;

            if (isset($this->themes[$themeRoot])) {
                $resources = array_merge($resources, $this->themes[$themeRoot]);
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

            $this->blocks->attach($themeRoot, $blocks);
        } else {
            $blocks = $this->blocks[$themeRoot];
        }
        return $blocks;
    }

}
