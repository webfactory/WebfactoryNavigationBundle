<?php

namespace Webfactory\Bundle\NavigationBundle\Build;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;

interface BuildDirector {
    public function build(BuildContext $c, Tree $t, BuildDispatcher $d);
}
