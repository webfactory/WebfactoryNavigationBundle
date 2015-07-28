<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Build;

class BuildContext
{

    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function change(array $params)
    {
        return new self(array_merge($this->params, $params));
    }

    public function get($name)
    {
        return @$this->params[$name];
    }

}
