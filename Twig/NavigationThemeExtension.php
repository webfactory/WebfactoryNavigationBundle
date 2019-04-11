<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webfactory\Bundle\NavigationBundle\Tree\Node;

class NavigationThemeExtension extends AbstractExtension
{
    protected $resources;

    /** @var \Twig_Template */
    protected $template;

    protected $themes;
    protected $blocks;

    public function __construct(array $resources = [])
    {
        $this->resources = $resources;
        $this->themes = new \SplObjectStorage();
        $this->blocks = new \SplObjectStorage();
    }

    public function getName()
    {
        return 'webfactory_navigation_theme_extension';
    }

    public function getTokenParsers()
    {
        return [
            new NavigationThemeTokenParser(),
        ];
    }

    public function setTheme(Node $themeRoot, array $resources)
    {
        $this->themes->attach($themeRoot, $resources);
        $this->blocks = new \SplObjectStorage();
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('power_set', [$this, 'getPowerSet']),
            new TwigFunction('navigation', [$this, 'renderNavigation'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_list', [$this, 'renderNavigationList'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_list_class', [$this, 'renderNavigationListClass'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_item', [$this, 'renderNavigationItem'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_item_class', [$this, 'renderNavigationItemClass'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_text', [$this, 'renderNavigationText'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_text_class', [$this, 'renderNavigationTextClass'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_url', [$this, 'renderNavigationUrl'], ['is_safe' => ['all'], 'needs_environment' => true]),
            new TwigFunction('navigation_caption', [$this, 'renderNavigationCaption'], ['is_safe' => ['all'], 'needs_environment' => true]),
        ];
    }

    public function getPowerSet(array $baseSet)
    {
        $count = \count($baseSet);
        $members = pow(2, $count);
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

    public function renderNavigation(\Twig_Environment $env, Node $node, $maxLevels, $expandedLevels)
    {
        return $this->renderNavigationList($env, $node, $node->getChildren(), 0, $maxLevels, $expandedLevels, $node);
    }

    public function renderNavigationList(
        \Twig_Environment $env,
        Node $themeRoot,
        array $nodes,
        $level,
        $maxLevels,
        $expandedLevels,
        Node $parentNode = null
    ) {
        if ($nodes && $level < $maxLevels && ($level < $expandedLevels || $parentNode->isActivePath())) {
            return $this->renderBlock($env, $themeRoot, 'navigation_list', get_defined_vars());
        }

        return '';
    }

    public function renderNavigationListClass(\Twig_Environment $env, Node $themeRoot, array $nodes, $level, Node $parentNode = null)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_list_class', get_defined_vars());
    }

    public function renderNavigationItem(\Twig_Environment $env, Node $themeRoot, Node $node, $level, $maxLevels, $expandedLevels, $loop)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_item', get_defined_vars());
    }

    public function renderNavigationItemClass(\Twig_Environment $env, Node $themeRoot, Node $node, $level, $loop)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_item_class', get_defined_vars());
    }

    public function renderNavigationText(\Twig_Environment $env, Node $themeRoot, Node $node, $level)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_text', get_defined_vars());
    }

    public function renderNavigationTextClass(\Twig_Environment $env, Node $themeRoot, Node $node, $level)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_text_class', get_defined_vars());
    }

    public function renderNavigationUrl(\Twig_Environment $env, Node $themeRoot, Node $node, $level)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_url', get_defined_vars());
    }

    public function renderNavigationCaption(\Twig_Environment $env, Node $themeRoot, Node $node, $level)
    {
        return $this->renderBlock($env, $themeRoot, 'navigation_caption', get_defined_vars());
    }

    public function renderBlock(\Twig_Environment $env, $themeRoot, $name, array $variables)
    {
        if (!$this->template) {
            $this->template = $env->loadTemplate(reset($this->resources));
        }

        $blocks = $this->getBlocks($env, $themeRoot);

        return $this->template->renderBlock($name, $variables, $blocks);
    }

    protected function getBlocks(\Twig_Environment $env, Node $themeRoot)
    {
        if (!$this->blocks->contains($themeRoot)) {
            $resources = $this->resources;

            if (isset($this->themes[$themeRoot])) {
                $resources = array_merge($resources, $this->themes[$themeRoot]);
            }

            $blocks = [];

            foreach ($resources as $resource) {
                if (!$resource instanceof \Twig_Template) {
                    $resource = $env->loadTemplate($resource);
                }
                $resourceBlocks = [];
                do {
                    $resourceBlocks = array_merge($resource->getBlocks(), $resourceBlocks);
                } while (false !== $resource = $resource->getParent([]));
                $blocks = array_merge($blocks, $resourceBlocks);
            }

            $this->blocks->attach($themeRoot, $blocks);
        } else {
            $blocks = $this->blocks[$themeRoot];
        }

        return $blocks;
    }
}
