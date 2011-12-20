<?php

namespace Webfactory\Bundle\NavigationBundle\Navigation;

interface UrlGeneratorInterface {

    public function generateNavigationNodePath(NodeInterface $node);

}