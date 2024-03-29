<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Event;

use Symfony\Component\HttpFoundation\RequestStack;

class ActiveNodeEventListener
{
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function initializeTree(TreeInitializedEvent $event): void
    {
        if ($masterRequest = $this->requestStack->getMainRequest()) {
            if ($node = $event->getTree()->find($masterRequest->attributes->all())) {
                $node->setActive();
            }
        }
    }
}
