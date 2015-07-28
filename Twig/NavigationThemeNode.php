<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Twig;

class NavigationThemeNode extends \Twig_Node
{

    public function __construct(
        \Twig_NodeInterface $navigation,
        \Twig_NodeInterface $resources,
        $lineNumber,
        $tag = null
    ) {
        parent::__construct(array('navigation' => $navigation, 'resources' => $resources), array(), $lineNumber, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->env->getExtension(\'webfactory_navigation_theme_extension\')->setTheme(')
            ->subcompile($this->getNode('navigation'))
            ->raw(', array(');

        foreach ($this->getNode('resources') as $resource) {
            $compiler
                ->subcompile($resource)
                ->raw(', ');
        }

        $compiler->raw("));\n");
    }
}
