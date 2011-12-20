<?php

namespace Webfactory\Bundle\NavigationBundle\Navigation;

use Webfactory\Tree\ActiveNode\ActiveNodeInterface;

interface NodeInterface extends ActiveNodeInterface {

    public function getCaption();
    public function getUrl();

}