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
    /**
     * @var array<string, mixed>
     */
    protected $params;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function change(array $params): self
    {
        return new self(array_merge($this->params, $params));
    }

    /**
     * @return mixed|null
     */
    public function get(string $name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }
}
