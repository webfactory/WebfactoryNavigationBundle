<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Twig;

use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class NavigationThemeTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();

        $navigation = $this->parser->getExpressionParser()->parseExpression();
        $resources = [];
        do {
            $resources[] = $this->parser->getExpressionParser()->parseExpression();
        } while (!$stream->test(Token::BLOCK_END_TYPE));

        $stream->expect(Token::BLOCK_END_TYPE);

        return new NavigationThemeNode($navigation, new Node($resources), $lineNumber, $this->getTag());
    }

    public function getTag(): string
    {
        return 'navigation_theme';
    }
}
