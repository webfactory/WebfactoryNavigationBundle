<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Twig\TokenParser\AbstractTokenParser;

class NavigationThemeTokenParser extends AbstractTokenParser
{
    public function parse(\Twig_Token $token)
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();

        $navigation = $this->parser->getExpressionParser()->parseExpression();
        $resources = [];
        do {
            $resources[] = $this->parser->getExpressionParser()->parseExpression();
        } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new NavigationThemeNode($navigation, new \Twig_Node($resources), $lineNumber, $this->getTag());
    }

    public function getTag()
    {
        return 'navigation_theme';
    }
}
