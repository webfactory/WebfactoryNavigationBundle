<?php
namespace Webfactory\Bundle\NavigationBundle\Event;

use Symfony\Component\HttpFoundation\RequestStack;
use Webfactory\Bundle\NavigationBundle\Event\TreeInitializedEvent;

class ActiveNodeEventListener
{
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function initializeTree(TreeInitializedEvent $event)
    {
        if ($masterRequest = $this->requestStack->getMasterRequest()) {
            if ($node = $event->getTree()->find($masterRequest->attributes->all())) {
                $node->setActive();
            }
        }
    }
}
