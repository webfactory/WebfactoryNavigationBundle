<?php

namespace Webfactory\Bundle\NavigationBundle\Build;

use Webfactory\Bundle\NavigationBundle\Tree\Tree;

/**
 * A BuildDirector is called by a BuildDispatcher to modify the NavigationTree. After that, he may call the Dispatcher
 * again to start the build, this is especially useful for any added nodes in the tree.
 */
interface BuildDirector
{
    public function build(BuildContext $c, Tree $t, BuildDispatcher $d);
}
