<?php

namespace Webfactory\Bundle\NavigationBundle\Twig;

class NavigationThemeTokenParser extends \Twig_TokenParser
{

    public function parse(\Twig_Token $token)
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();

        $navigation = $this->parser->getExpressionParser()->parseExpression();
        $resources = array();
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
