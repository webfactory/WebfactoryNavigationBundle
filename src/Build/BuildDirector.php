<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Build;

use Webfactory\Bundle\NavigationBundle\Tree\Tree;

/**
 * A BuildDirector is called by a BuildDispatcher to modify the NavigationTree. After that, he may call the Dispatcher
 * again to start the build, this is especially useful for any added nodes in the tree.
 */
interface BuildDirector
{
    /**
     * @return void
     */
    public function build(BuildContext $c, Tree $t, BuildDispatcher $d);
}
