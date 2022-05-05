<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Twig\Compiler;
use Twig\Node\Node;

class NavigationThemeNode extends Node
{
    public function __construct(
        Node $navigation,
        Node $resources,
        $lineNumber,
        $tag = null
    ) {
        parent::__construct(['navigation' => $navigation, 'resources' => $resources], [], $lineNumber, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->env->getExtension(\''.NavigationThemeExtension::class.'\')->setTheme(')
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
