<?php

namespace Webfactory\Bundle\NavigationBundle\Navigation;

use Webfactory\Tree\ActiveNode\ActiveNodeInterface;

interface NodeInterface {

    public function getCaption();
    public function getUrl();

}